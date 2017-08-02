<?php
class sfRule extends xPDOSimpleObject {

    public $config = array();


    public function save($cacheFlag = null)
    {
        $this->set('editedon',strtotime(date('Y-m-d H:i:s')));
        return parent::save($cacheFlag);
    }


    /**
     * Returns true url for rwo and more filterm params
     *
     * @param int $returnArray = 0
     *
     * @return string
     */
    public function generateUrl($returnArray = 0) {
        $separator = $this->xpdo->getOption('seofilter_separator', null, '-', true);
        $url = '';
        $urls = array();
        $count = 1;
        $rule_id = $this->get('id');
        $q = $this->xpdo->newQuery('sfFieldIds');
        $q->sortby('priority', 'ASC');
        if($links = $this->getMany('Links',$q)){
            foreach($links as $key => $link) {
                if($field = $link->getOne('Field')) {
                    $field_id = $field->get('id');
                    $aliases = array();
                    if($alias = $field->get('alias')) {
                        if($field->get('hideparam')) {
                            $url .= '{$'.$alias.'}/';
                        } elseif($field->get('valuefirst')){
                            $url .= '{$'.$alias.'}' . $separator . $alias. '/';
                        } else {
                            $url .= $alias . $separator . '{$'.$alias.'}/';
                        }
                        if($count == count($links)) {
                            $url = substr($url, 0,-1);
                        }

                        if($returnArray) {
                            $q = $this->xpdo->newQuery('sfDictionary');
                            $q->where(array('field_id'=>$field_id));
                            if($link->get('where') && $link->get('compare') && $link->get('value')) {
                                $value = $link->get('value');
                                $values = explode(',',$value);
                                switch ($link->get('compare')) {
                                    case 1:
                                        $q->where(array('input:IN'=>$values));
                                        break;
                                    case 2:
                                        $q->where(array('input:NOT IN'=>$values));
                                        break;
                                    case 3:
                                        $q->where(array('input:>'=>$value));
                                        break;
                                    case 4:
                                        $q->where(array('input:<'=>$value));
                                        break;
                                    case 5:
                                        $q->where(array('input:>'=>$values[0],'AND:input:<'=>$values[1]));
                                        break;
                                }
                            }

                            $words = $field->getMany('Words',$q);
                            foreach($words as $word) {
                                //$this->xpdo->log(modX::LOG_LEVEL_ERROR,print_r($word->toArray(),1));
                                $all_array = array(
                                    'url' => $word->get('alias'),
                                    'field_word' => array(array(
                                        'field_id' => $field_id,
                                        'word_id' => $word->get('id'),
                                        //'word_alias' => $word->get('alias'),
                                    ))
                                );
                                $aliases[$key][] = $all_array;
                                //$aliases[$key][] = $word->get('alias');
                            }
                            foreach($aliases[$key] as $akey => $avalue) {
                                if($field->get('hideparam')) {
                                     $add_url = $avalue['url'].'/';
                                } else {
                                     $add_url = $alias . $separator .$avalue['url'].'/';
                                }
                                if($count == count($links)) {
                                    $add_url = substr($add_url, 0,-1);
                                }
                                $avalue['url'] = $add_url;
                                $urls[$key][] = $avalue;
                                //$urls[$key][] = $add_url;
                            }
                        }
                        $count++;
                    }
                }
            }
        }
        //$this->xpdo->log(modx::LOG_LEVEL_ERROR, 'SEOFILTER: '. print_r($url,1));
        if(!$returnArray) {
            return $url;
        } else {
            $urls = array_values($urls);
            $count = count($urls);
            $urls_array = array();
            if($count) {
                $urls_array = $urls[0];
                for($i=1;$i<$count;$i++) {
                    $urls_array = $this->matrixmult($urls_array,$urls[$i]);
                }
            }
            //$this->xpdo->log(modx::LOG_LEVEL_ERROR, 'SEOFILTER: '. print_r($urls_array,1));
            return $urls_array;

        }
    }



    public function matrixmult($a1,$a2) {
        $r=count($a1);
        $c=count($a2);
        $a3 = array();
        for ($i=0;$i< $r;$i++){
            for($j=0;$j<$c;$j++){
                $arr = array(
                    'url' => $a1[$i]['url'] . $a2[$j]['url'],
                    'field_word' => array_merge($a1[$i]['field_word'],$a2[$j]['field_word']),
                );
                //$a3[] = $a1[$i] . $a2[$j];
                $a3[] = $arr;
            }
        }
        return $a3;
    }




}