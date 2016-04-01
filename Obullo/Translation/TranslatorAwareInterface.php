<?php

namespace Obullo\Translation;

use Obullo\Translation\TranslatorInterface as Translator;

/**
 * Immutable translator aware interface
 */
interface TranslatorAwareInterface
{
    /**
     * Set translator
     * 
     * @param Translator $translator object
     *
     * @return void
     */
    public function setTranslator(Translator $translator);

    /**
     * Returns to translator object
     * 
     * @return object
     */
    public function getTranslator();
}
