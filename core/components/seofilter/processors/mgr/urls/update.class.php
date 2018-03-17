<?php

class sfUrlsUpdateProcessor extends modObjectUpdateProcessor
{
    public $objectType = 'sfUrls';
    public $classKey = 'sfUrls';
    public $languageTopics = array('seofilter');
    //public $permission = 'save';


    /**
     * We doing special check of permission
     * because of our objects is not an instances of modAccessibleObject
     *
     * @return bool|string
     */
    public function beforeSave()
    {
        if (!$this->checkPermissions()) {
            return $this->modx->lexicon('access_denied');
        }

        $this->object->set('editedon',strtotime(date('Y-m-d H:i:s')));

        return true;
    }


    /**
     * @return bool
     */
    public function beforeSet()
    {

        $id = (int)$this->getProperty('id');
       // $name = trim($this->getProperty('name'));
        if (empty($id)) {
            return $this->modx->lexicon('seofilter_url_err_ns');
        }

//        if (empty($name)) {
//            $this->modx->error->addField('name', $this->modx->lexicon('seofilter_url_err_name'));
//        } elseif ($this->modx->getCount($this->classKey, array('name' => $name, 'id:!=' => $id))) {
//            $this->modx->error->addField('name', $this->modx->lexicon('seofilter_url_err_ae'));
//        }

        return parent::beforeSet();
    }


    public function afterSave()
    {
        if((int)$this->getProperty('recount')) {
            $SeoFilter = $this->modx->getService('seofilter', 'SeoFilter', $this->modx->getOption('seofilter_core_path', null,
                    $this->modx->getOption('core_path') . 'components/seofilter/') . 'model/seofilter/', array());
            $SeoFilter->loadHandler();

            $old_total = $this->object->get('total');
            $total = $SeoFilter->countHandler->countByLink($this->object->get('id'));
            $this->object->set('total',$total);
            if($old_total != $total) {
                $this->object->set('editedon',strtotime(date('Y-m-d H:i:s')));
            }
            $this->object->save();
        }
        return parent::afterSave();
    }

    public function cleanup()
    {
        $url = '';
        if ($this->getProperty('frame')) {
            $array = $this->object->toArray();
            //$this->modx->log(modX::LOG_LEVEL_ERROR, print_r($this->getProperties(), 1));
            if (($array['old_url'] || $array['new_url']) && $array['page_id']) {
                if (!($addurl = $array['new_url'])) {
                    $addurl = $array['old_url'];
                }
                $url = $this->modx->makeUrl($array['page_id'], '', '', 'full') . $addurl;
            }
        }
        return $this->success($url, $this->object);
    }
}

return 'sfUrlsUpdateProcessor';
