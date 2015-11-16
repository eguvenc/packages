<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <link href="<?php echo $this->url->asset('css/welcome.css') ?>" rel="stylesheet" type="text/css" />
        <title>ReCaptcha Form Example</title>
        <?php echo $this->recaptcha->printJs() ?>
    </head>

    <body>
        <header>
            <?php echo $this->url->anchor('/', '<img src="'.$this->url->asset('images/logo.png').'">') ?>
        </header>

        <h1>ReCaptcha Form Example</h1>

        <?php echo $this->form->getMessage() ?>

        <section>
            <form action="/recaptcha/examples/form" method="POST">
                <table width="100%">
                    <tr>
                        <td style="width:20%;"></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Username</td>
                        <td>
                        <?php echo $this->form->getError('username', '<span class="_inputError">', '</span>') ?>
                        <input type="text" name="username" value="<?php echo $this->form->getValue('username')?>" />
                        </td>
                    </tr>
                    <tr>
                        <td>Captcha</td>
                        <td>
                        <?php echo $this->form->getError($this->recaptcha->getInputName(), '<span class="_inputError">', '</span>') ?>
                        <?php echo $this->recaptcha->printHtml() ?>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><input type="submit" name="dopost" value="DoPost" /></td>
                    </tr>
                    <tr>
                        <td colspan="2">&nbsp;</td>
                    </tr>
                    </table>
                </form>

                <h2>Form Results</h2>

                <section>
                    <h3>$this->form->outputArray()</h3>
                    <pre><?php print_r($this->form->outputArray()); ?></pre>

                    <h3>$this->form->getError()</h3>
                    <pre><?php echo $this->form->getError($this->recaptcha->getInputName()) ?></pre>
                </section>
        </section>

        <p>&nbsp;</p>

    </body>
</html>
