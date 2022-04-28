<?php

namespace app\component;

use Phalcon\Di\Injectable;
use Phalcon\Translate\Adapter\NativeArray;
use Phalcon\Translate\InterpolatorFactory;
use Phalcon\Translate\TranslateFactory;

class Locale extends Injectable
{
    /**
     * @return NativeArray
     */
    public function getTranslator(): NativeArray
    {
        // Ask browser what is the best language
        // $language = $this->request->getBestLanguage();
        // $messages = [];
        // $language='en';
        $language=$this->request->getQuery("locale");
        
        
        $translationFile = '../app/message/' . $language . '.php';

        if (true !== file_exists($translationFile)) {
            // $translationFile = 'app/messages/en.php';
            $translationFile = '../app/message/en.php';
        }
        
        require $translationFile;

        $interpolator = new InterpolatorFactory();
        $factory      = new TranslateFactory($interpolator);
        
        return $factory->newInstance(
            'array',
            [
                'content' => $messages,
            ]
        );
    }
}

?>