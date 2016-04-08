<?php

namespace Obullo\Translation;

use Obullo\Translation\TranslatorInterface as Translator;

/**
 * Immutable translator aware trait
 */
trait TranslatorAwareTrait
{
    /**
     * Translator
     * 
     * @var object
     */
    protected $translator;

    /**
     * Set translator
     * 
     * @param Translator $translator object
     *
     * @return void
     */
    public function setTranslator(Translator $translator)
    {
        $this->translator = null;
        $this->translator = $translator;
    }

    /**
     * Returns to translator object
     * 
     * @return object
     */
    public function getTranslator()
    {
        return $this->translator;
    }
}
