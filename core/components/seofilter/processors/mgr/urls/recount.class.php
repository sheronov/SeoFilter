<?php

class sfUrlsReCountProcessor extends modObjectProcessor
{
    public $objectType = 'sfUrls';
    public $classKey = 'sfUrls';
    public $languageTopics = array('seofilter');
    //public $permission = 'save';


    /**
     * @return array|string
     */
    public function process()
    {
        if (!$this->checkPermissions()) {
            return $this->failure($this->modx->lexicon('access_denied'));
        }

        $ids = $this->modx->fromJSON($this->getProperty('ids'));
        $all = (int)$this->getProperty('all');
        if (empty($ids) && !$all) {
            return $this->failure($this->modx->lexicon('seofilter_url_err_ns'));
        }

        $SeoFilter = $this->modx->getService('seofilter', 'SeoFilter', $this->modx->getOption('seofilter_core_path', null,
                $this->modx->getOption('core_path') . 'components/seofilter/') . 'model/seofilter/', array());
        $SeoFilter->loadHandler();

        if($all) {
            $links = $this->modx->getIterator('sfUrls');
            foreach($links as $object) {
                $id = $object->get('id');
                $old_total = $object->get('total');
                $total = $SeoFilter->countHandler->countByLink($id);
                $object->set('total', $total);
                if ($old_total != $total) {
                    $object->set('editedon', strtotime(date('Y-m-d H:i:s')));
                }
                $object->save();
            }
        } else {
            foreach ($ids as $id) {
                /** @var sfUrls $object */
                if (!$object = $this->modx->getObject($this->classKey, $id)) {
                    return $this->failure($this->modx->lexicon('seofilter_url_err_nf'));
                }

                $old_total = $object->get('total');

                $total = $SeoFilter->countHandler->countByLink($id);
                $object->set('total', $total);

                if ($old_total != $total) {
                    $object->set('editedon', strtotime(date('Y-m-d H:i:s')));
                }

                $object->save();
            }
        }

        return $this->success();
    }

}

return 'sfUrlsReCountProcessor';
