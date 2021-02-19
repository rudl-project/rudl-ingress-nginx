<?php


namespace Rudl\IngressNginx;


use Rudl\LibGitDb\Type\Ingress\T_IngressConfig;

class Template
{

    private $isUpdated = false;

    public function __construct(
        private string $templateFile,
        private string $targetFile
    ){}


    /**
     *
     *
     * @param $__CONF
     * @return bool
     */
    public function parse(T_IngressConfig $__CONF) : bool
    {
        $this->isUpdated = false;
        ob_start();
        require $this->templateFile;
        $content = ob_get_clean();

        if ($content !== file_get_contents($this->targetFile)) {
            $this->isUpdated = true;
            file_put_contents($this->targetFile, $content);
            return true;
        }
        return false;
    }

}