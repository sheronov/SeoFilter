<?php
class sfMultiField extends xPDOSimpleObject {

    public $config = array();

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
        $q = $this->xpdo->newQuery('sfFieldIds');
        $q->sortby('priority', 'ASC');
        if($links = $this->getMany('Link',$q)){
            foreach($links as $key => $link) {
//                if($link->get('where')) {
//                    $where = 1;
//                }
                if($field = $link->getOne('Field')) {
                    $aliases = array();
                    if($alias = $field->get('alias')) {

                        if($field->get('hideparam')) {
                            $url .= '/{$'.$alias.'}';
                        } else {
                            $url .= '/' . $alias . $separator . '{$'.$alias.'}';
                        }

                        if($returnArray) {
                            $words = $field->getMany('Dictionary');
                            foreach($words as $word) {
                                $aliases[$key][] = $word->get('alias');
                            }
                            foreach($aliases[$key] as $akey => $avalue) {
                                if($field->get('hideparam')) {
                                    $urls[$key][] .=  '/'.$avalue;
                                } else {
                                    $urls[$key][] .= '/' . $alias . $separator .$avalue;
                                }
                            }
                        }
                    }
                }
            }
        }
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
            return $urls_array;
        }
    }

    public function matrixmult($a1,$a2) {
        $r=count($a1);
        $c=count($a2);
        $a3 = array();
        for ($i=0;$i< $r;$i++){
            for($j=0;$j<$c;$j++){
                $a3[] = $a1[$i] . $a2[$j];
            }
        }
        return $a3;
    }




}