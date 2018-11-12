<?php
class sfUrls extends xPDOSimpleObject {



    public function updateUrl($priorities = array(), $words = array(),$feilds = array()) {

        $new_url = array();
        $separator = $this->xpdo->getOption('seofilter_separator', null, '-', true);

        if($this->get('new_url')) {
            $url = $this->get('new_url');
        } else {
            $url = $this->get('old_url');
        }

        $q = $this->xpdo->newQuery('sfUrlWord');
        $q->sortby('priority','ASC');
        $q->leftJoin('sfField','sfField','sfUrlWord.field_id = sfField.id');
        $q->leftJoin('sfDictionary','sfDictionary','sfUrlWord.word_id = sfDictionary.id');
        $q->where(array('sfUrlWord.url_id'=>$this->get('id')));
        $q->select('sfUrlWord.id, sfField.id as field_id, sfField.alias as field_alias, sfField.hideparam, sfField.valuefirst, sfDictionary.id as word_id, sfDictionary.alias as word_alias');
        if($q->prepare() && $q->stmt->execute()) {
            while($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                if($row['hideparam']) {
                    $add_url = $row['word_alias'];
                } else if ($row['valuefirst']) {
                    $add_url = $row['word_alias'].$separator.$row['field_alias'];
                } else {
                    $add_url = $row['field_alias'].$separator.$row['word_alias'];
                }
                if($priorities) {
                    $priority = array_search($row['field_id'],$priorities);
                    $new_url[$priority] = $add_url;
                    unset($priorities[$priority]);
                } else {
                    $new_url[] = $add_url;
                }
            }
        }
        $this->set('editedon',strtotime(date('Y-m-d H:i:s')));
        $this->set('old_url',implode('/',$new_url));
        $this->save();
        return implode('/',$new_url);
    }


}