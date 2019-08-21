<?php
class sfFieldIds extends xPDOSimpleObject {

    public function updateUrlMask($rule_id = 0) {
        $separator = $this->xpdo->getOption('seofilter_separator', null, '-', true);
        $level_separator = $this->xpdo->getOption('seofilter_level_separator', null, '/', true);

        $urls = array();
        $q = $this->xpdo->newQuery('sfFieldIds');
        $q->where(array('multi_id'=>$rule_id));
        $q->sortby('priority','ASC');
        $q->innerJoin('sfField','Field','Field.id = sfFieldIds.field_id');
        $q->select(array(
            'Field.*'
        ));
        if($q->prepare() && $q->stmt->execute()) {
            while($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                $var = '{$'.$row['alias'].'}';


                if($row['hideparam']) {
                    $urls[] = $var;
                } elseif($row['valuefirst']) {
                    $urls[] = $var.$separator.$row['alias'];
                } else {
                    $urls[] = $row['alias'].$separator.$var;
                }
            }
        }

        $url = implode($level_separator,$urls);

        return $url;
    }

}