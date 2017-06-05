<?php
class sfMultiField extends xPDOSimpleObject {

    /**
     * Returns true url for rwo and more filterm params
     *
     * @param int $generate = 1
     *
     * @return string
     */
    public function makeUrl($generate = 1) {
        $url = '';

        $q = $this->xpdo->newQuery('sfFieldIds');
        $q->sortby('priority', 'ASC');
        if($links = $this->getMany('Link',$q)){
            $count = count($links);
            foreach($links as $key => $link) {
                if($link->get('where')) {
                    $where = 1;
                }
                if($field = $link->getOne('Field')) {
                    if($alias = $field->get('alias')) {
                        if($field->get('hideparam')) {
                            $url .= '/{$'.$alias.'}';
                        } else {
                            $url .= '/' . $alias . '-{$'.$alias.'}';
                        }
                    }
                    if($generate == 1) {
                        $words = $field->getMany('Dictionary');
                        foreach($words as $word) {
                            $walias = $word->get('alias');
                            $this->xpdo->log(modx::LOG_LEVEL_ERROR, print_r($word->toArray(),1));
                        }
                    }
                }
            }
        }


        //$this->xpdo->log(modx::LOG_LEVEL_ERROR, print_r($this->toArray(),1).'genereate = '.$generate);

        return $url;

    }

}