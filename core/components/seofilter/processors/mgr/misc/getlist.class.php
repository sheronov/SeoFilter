<?php

class modMgrMiscGetlistProcessor extends modProcessor
{

    public function process()
    {
        $id = $this->getProperty('id');
        $dir = $this->getProperty('dir');
        if(empty($dir)) {
            $dir = 'core';
        }

        if(!isset($contextKey) || empty($contextKey)) {
            $contextKey = 'web';
        }
        // get base path
        $basePathSetting = $this->modx->getObject('modContextSetting', array(
            'context_key'=> $contextKey,
            'key'=> 'base_path'
        ));
        $basePath = $this->modx->getOption('base_path');
        if($basePathSetting !== null) {
            $basePath = $basePathSetting->get('value');
        }
        // make sure specified folder exists ($dir passed from @EVAL
        if (substr($basePath,-1,1) != '/') { $basePath .= '/'; }
        if (substr($dir,-1,1) != '/') { $dir .= '/'; }
        $targetPath = $basePath . str_replace('..', '.', $dir);

        // do @DIRECTORY logic
        if (!is_dir($targetPath)) { return ''; }
        $files = array();
        $invalid = array('.','..','.svn','.git','.DS_Store');
        $i = 0;
        foreach (new DirectoryIterator($targetPath) as $file) {
            if (!$file->isReadable()) continue;
            $i++;
            $basename = $file->getFilename();
            if(!in_array($basename,$invalid)) {
                if(strpos($basename,'.')){
                    $names = explode('.',$basename);
                    $name = $names[0];
                } else {
                    $name = $basename;
                }
                if(!empty($id)) {
                    if($id != $basename) {
                        continue;
                    }
                }
                $files[] = array('idx'=>$i,'id'=>$basename,'name'=>$this->modx->lexicon('seofilter_tpl_'.$name));
            }
        }
        asort($files);

        return $this->outputArray($files);
    }

}