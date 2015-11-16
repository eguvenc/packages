<?php

namespace Obullo\Cli\Task;

use RuntimeException;
use Obullo\Cli\Controller;
use Obullo\Cli\Console;

class Debugger extends Controller
{
    protected $socket;
    protected $msg;
    protected $length;
    protected $connection;
    protected $maxByte = 10242880;  // 10 Mb
    protected $clients = array();

    /**
     * Loader
     * 
     * @return void
     */
    public function __construct()
    {
        self::registerErrorHandler();     // We disable errors otherwise we get socket write errors in ajax response
        self::registerExceptionHandler();
    }

    /**
     * Print logo
     * 
     * @return string
     */
    public function logo()
    {
        echo Console::logo("Welcome to Debug Manager (c) 2015");
        echo Console::description("You are running \$php task debugger command. For help type php task debugger help");
        echo Console::text('running ...');
    }

    /**
     * Write iframe
     *  
     * @return void
     */
    public function index()
    {
        $this->logo();

        ob_implicit_flush();   /* Turn on implicit output flushing so we see what we're getting as it comes in. */

        if (false == preg_match('#(ws:\/\/(?<host>(.*)))(:(?<port>\d+))(?<url>.*?)$#i', $this->c['config']['http']['debugger']['socket'], $matches)) {
            throw new RuntimeException("Debugger socket connection error, example web socket configuration: ws://127.0.0.1:9000");
        }
        $this->connection = $matches;
        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);   // Create TCP/IP sream socket
        socket_set_option($this->socket, SOL_SOCKET, SO_REUSEADDR, 1);  // Reuseable port
        socket_bind($this->socket, 0, $this->connection['port']);       // Bind socket to specified host
        socket_listen($this->socket);  // Listen to port

        $this->clients = array($this->socket);
        $this->process();
    }

    /**
     * Start background process
     * 
     * @return void
     */
    public function process()
    {
        $host = $this->connection['host'];
        $port = $this->connection['port'];
        $url  = $this->connection['url'];
        $null = null;

        while (true) {

            $connections = $this->clients; // Manage multiple connections

            foreach ($connections as $k => $socket) {
                if (get_resource_type($socket) != 'Socket') {
                    unset($connections[$k], $this->clients[$k]);
                }
            }
            if (in_array($this->socket, $connections)) {   // Check for new socket

                // WARNING : Socket select must be declared after that in_array
                // otherwise we get cpu usage warning !!!

                if (socket_select($connections, $null, $null, 0) < 1) {
                    sleep(1);  // WARNING : Don't increase the cpu usage !!!
                    // continue;
                }

                // Add socket to client array

                if (($this->clients[] = $newSocket = socket_accept($this->socket)) === false) {
                    echo "socket_accept() failed: " . socket_strerror(socket_last_error($newSocket)) . "\n";
                    break;
                }
                
                $header = socket_read($newSocket, $this->maxByte); // Read data sent by the socket
                $headers = $this->handshake($header, $newSocket, $host, $port, $url); // Perform websocket handshake
                
                if ($headers == false) {
                    // ..
                }

                if (is_array($headers) && isset($headers['Request'])) {
                    $sent = $this->sendRequest($headers, $newSocket);
                    if ($sent == false) {
                        // ..
                    }
                }
                // remove the listening socket from the clients-with-data array

                $foundSocket = array_search($this->socket, $connections);
                unset($connections[$foundSocket]);
            }

            // No need to broadcast
            // $this->readStreamResources($connections);  // Read socket data
        }
        socket_close($this->socket);
    }

    /**
     * Send request to stream
     * 
     * @param array    $headers   request headers
     * @param resource $newSocket socket resource
     * 
     * @return void
     */
    protected function sendRequest($headers, $newSocket)
    {
        $data = [
            'type' => 'system',
            'socket' => intval($newSocket)
        ];
        if (isset($headers['Msg-id'])) {
            $data['id'] = $headers['Msg-id'];
        }
        if (isset($headers['Environment-data'])) {
            $data['env'] = $headers['Environment-data'];
        }
        if (isset($headers['Response-data'])) {
            $data['output'] = $headers['Response-data'];
        }
        if (isset($headers['Log-data'])) {
            $data['log'] = $headers['Log-data'];
        }
        if (isset($headers['Page-css'])) {
            $data['css'] = $headers['Page-css'];
        }
        if (isset($headers['Page-uri'])) {
            $data['uri'] = $headers['Page-uri'];
        }
        if ($headers['Request'] == 'Http') {
            $data['message'] = 'HTTP_REQUEST';
            return $this->send($data);
        } elseif ($headers['Request'] == 'Ajax') {
            $data['message'] = 'AJAX_REQUEST';
            return $this->send($data);
        } elseif ($headers['Request'] == 'Cli') {
            $data['message'] = 'CLI_REQUEST';
            return $this->send($data);
        }
        return false;
    }

    /**
     * Send data to all clients
     * 
     * @param string $data data
     * 
     * @return void
     */
    public function send($data)
    {
        $responseText = static::mask(json_encode($data));
        return $this->broadcast($responseText);    // Send data
    }

    /**
     * Broadcast message to all connections
     * 
     * @param string $msg message
     * 
     * @return void
     */
    public function broadcast($msg)
    {
        $this->msg = $msg;
        $this->length = strlen($this->msg);

        $sent = true;
        foreach ($this->clients as $socket) {
            if (is_resource($socket) && get_resource_type($socket) == 'Socket') {
                $write = $this->socketWrite($socket);
                if ($write == false) {
                    $sent = false;
                }
            }
        }
        return $sent;
    }

    /**
     * Silent Errors
     * 
     * Register debugger system as an error handler PHP errors
     * 
     * @return mixed Returns result of set_error_handler
     */
    public static function registerErrorHandler()
    {
        return set_error_handler(
            function ($level, $message, $file, $line) {
                // echo $message.' Line:'.$line."\n";
                return $level = $message = $file = $line = 0;
                return;
            }
        );
    }

    /**
     * Silent Exceptions
     * 
     * Register logging system as an exception handler to log PHP exceptions
     * 
     * @return boolean
     */
    public static function registerExceptionHandler()
    {
        set_exception_handler(
            function ($e) {
                echo $e->getMessage();
                return $e = null;
            }
        );
    }

    /**
     * Encode message for transfer to client.
     * 
     * @param string $text message
     * 
     * @return string
     */
    protected static function mask($text)
    {
        $b1 = 0x80 | (0x1 & 0x0f);
        $length = strlen($text);
        if($length <= 125)
            $header = pack('CC', $b1, $length);
        elseif($length > 125 && $length < 65536)
            $header = pack('CCn', $b1, 126, $length);
        elseif($length >= 65536)
            $header = pack('CCNN', $b1, 127, $length);
        return $header.$text;
    }

    /**
     * Unmask incoming framed message
     * 
     * @param string $text message
     * 
     * @return string
     */
    public static function unmask($text)
    {
        $length = ord($text[1]) & 127;
        if ($length == 126) {
            $masks = substr($text, 4, 4);
            $data = substr($text, 8);
        } elseif ($length == 127) {
            $masks = substr($text, 10, 4);
            $data = substr($text, 14);
        } else {
            $masks = substr($text, 2, 4);
            $data = substr($text, 6);
        }
        $text = "";
        for ($i = 0; $i < strlen($data); ++$i) {
            $text .= $data[$i] ^ $masks[$i%4];
        }
        return $text;
    }

    /**
     * Handshake with client
     * 
     * @param array    $header request headers
     * @param resource $socket socket connection
     * @param string   $host   address
     * @param integer  $port   port number
     * @param string   $url    any possible url address
     * 
     * @return boolean
     */
    protected function handshake($header, $socket, $host, $port, $url)
    {
        $headers = array();
        $lines = preg_split("/\r\n/", $header);
        foreach ($lines as $line) {
            $line = chop($line);
            if (preg_match('/\A(\S+): (.*)\z/', $line, $matches)) {
                $headers[$matches[1]] = $matches[2];
            }
        }
        if (isset($headers['Sec-WebSocket-Key'])) {
            $secKey = $headers['Sec-WebSocket-Key'];
            $secAccept = base64_encode(pack('H*', sha1($secKey . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11')));
            $upgrade  = "HTTP/1.1 101 Web Socket Protocol Handshake\r\n" .
            "Upgrade: websocket\r\n" .
            "Connection: Upgrade\r\n" .
            "WebSocket-Origin: $host\r\n" .
            "WebSocket-Location: ws://$host:$port$url\r\n".
            "Sec-WebSocket-Accept:$secAccept\r\n\r\n";

            $this->msg = $upgrade;
            $this->length = strlen($this->msg);
            return $this->socketWrite($socket);
        }
        return $headers;
    }

    /**
     * Write to socket
     * 
     * @param resource $socket socket
     * 
     * @return boolean
     */
    public function socketWrite($socket)
    {
        $sent = socket_write($socket, $this->msg, $this->length);
        if ($sent === false) {
            return false;
        }
        // Check if the entire message has been sented
        if ($sent < $this->length) {
                
            // If not sent the entire message.
            // Get the part of the message that has not yet been sented as message
            $this->msg = substr($this->msg, $sent);
                
            // Get the length of the not sented part
            $this->length -= $sent;

        } else {   
            return false;
        }
        return true;
    }

    /**
     * Cli help
     * 
     * @return void
     */
    public function help()
    {
        $this->logo();

echo Console::help("Help:", true);
echo Console::newline(2);
echo Console::help("
Available Commands

    debugger     : Run debug server."
);
echo Console::newline(2);
echo Console::help("Usage:", true);
echo Console::newline(2);
echo Console::help("php task debugger");
echo Console::newline(2);
echo Console::help("Description:", true);
echo Console::newline(2);
echo Console::help("Start debugger websocket server.");
echo Console::newline(2);
    }

}