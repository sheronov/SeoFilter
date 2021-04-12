<?php

class sfUrlsRemoveProcessor extends modObjectProcessor
{
    public $objectType        = 'seofilter.url';
    public $classKey          = 'sfUrls';
    public $languageTopics    = ['seofilter'];
    public $beforeRemoveEvent = 'sfOnUrlBeforeRemove';
    public $afterRemoveEvent  = 'sfOnUrlRemove';
    //public $permission = 'remove';


    /**
     * @return array|string
     */
    public function process()
    {
        if (!$this->checkPermissions()) {
            return $this->failure($this->modx->lexicon('access_denied'));
        }

        $ids = $this->modx->fromJSON($this->getProperty('ids'));
        if (empty($ids)) {
            return $this->failure($this->modx->lexicon('seofilter_url_err_ns'));
        }

        foreach ($ids as $id) {
            /** @var sfField $object */
            if (!$object = $this->modx->getObject($this->classKey, $id)) {
                return $this->failure($this->modx->lexicon('seofilter_url_err_nf'));
            }

            $preventRemoval = $this->fireBeforeRemoveEvent($object);
            if (!empty($preventRemoval)) {
                return $this->failure($preventRemoval);
            }
            $object->remove();
            $this->fireAfterRemoveEvent($id, $object);
        }

        return $this->success();
    }

    public function fireBeforeRemoveEvent($object)
    {
        $preventRemove = false;
        if (!empty($this->beforeRemoveEvent)) {
            $response = $this->modx->invokeEvent($this->beforeRemoveEvent, [
                'id'              => $object->get('id'),
                $this->classKey   => &$object,
                'object'          => &$object,
            ]);
            $preventRemove = $this->processEventResponse($response);
        }
        return $preventRemove;
    }

    public function fireAfterRemoveEvent($id, $object = null)
    {
        if (!empty($this->afterRemoveEvent)) {
            $this->modx->invokeEvent($this->afterRemoveEvent, [
                'id'              => $id,
                $this->classKey   => &$object,
                'object'          => &$object,
            ]);
        }
    }

}

return 'sfUrlsRemoveProcessor';