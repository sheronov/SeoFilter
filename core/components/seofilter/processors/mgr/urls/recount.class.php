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
        $response = array();
        $ids = $this->modx->fromJSON($this->getProperty('ids'));
        $all = (int)$this->getProperty('all');
        $remove = (int)$this->getProperty('remove');
        $last_id = (int)$this->getProperty('last_id');
        if (empty($ids) && !$all) {
            return $this->failure($this->modx->lexicon('seofilter_url_err_ns'));
        }


        $SeoFilter = $this->modx->getService('seofilter', 'SeoFilter', $this->modx->getOption('seofilter_core_path', null,
                $this->modx->getOption('core_path') . 'components/seofilter/') . 'model/seofilter/', array());
        $SeoFilter->loadHandler();

        $q = $this->modx->newQuery('sfUrls');
        $q->sortby('id','ASC');
        if($remove) {
            $q->where(array('id:>'=>$last_id));
        }
        if($all) {
            $total = $this->modx->getCount('sfUrls',$q);
            if(!$limit = $this->getProperty('limit')) {
                $limit = 100;
            }
            if(!$offset = $this->getProperty('offset')) {
                $offset = 0;
            }
            if((int)$this->getProperty('cron')) {
                $q->limit(0);
            } else {
                $q->limit($limit,$offset);
            }

            $offset += $limit;
            if($offset >= $total) {
                $percent = 1;
                $done = true;
            } else {
                $done = false;
                $percent = $offset / $total;
            }
            $response = array('done'=>$done,'limit'=>$limit,'offset'=>$offset,'total'=>$total,'value'=>$percent,'text'=>"{$offset}/{$total}");
        } else {
            $q->where(array('id:IN'=>$ids));
        }
        if($remove) {
            $response['remove'] = 0;
        }
        $links = $this->modx->getIterator('sfUrls',$q);
        foreach($links as $object) {
            $id = $object->get('id');
            $old_total = $object->get('total');
            $page_id = $object->get('page_id');
            $total = $SeoFilter->countHandler->countByLink($id,$page_id);
            $object->set('total', $total);
            if ($old_total != $total) {
                $object->set('editedon', strtotime(date('Y-m-d H:i:s')));
            }
            if($remove && $total==0) {
                $response['remove']++;
                $response['offset']--;
                $object->remove();
            } else {
                $object->save();
            }
        }

        if($remove) {
            $response['text'] = "{$response['offset']}/{$response['total']}";
        }


        return $this->success('',$response);
    }

}

return 'sfUrlsReCountProcessor';
