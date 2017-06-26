<?php
class sfUrls extends xPDOSimpleObject {

    public function save($cacheFlag = null)
    {
        if($this->get('createdon')) {
            $this->set('editedon',strtotime(date('Y-m-d H:i:s')));
        } else {
            $this->set('createdon',strtotime(date('Y-m-d H:i:s')));
        }

        return parent::save($cacheFlag);
    }
}