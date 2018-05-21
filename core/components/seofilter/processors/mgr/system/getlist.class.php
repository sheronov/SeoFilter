<?php
class msResourceGetListProcessor extends modObjectGetListProcessor
{
    public $classKey = 'modResource';
    public $languageTopics = array('resource');
    public $defaultSortField = 'pagetitle';
    /**
     * @param xPDOQuery $c
     *
     * @return xPDOQuery
     */
    public function prepareQueryBeforeCount(xPDOQuery $c)
    {

        if($this->getProperty('fromUrls')) {
            $c->select('id,pagetitle');
            $q = $this->modx->newQuery('sfUrls');
            $q->select('DISTINCT sfUrls.page_id');
            if($q->prepare() && $q->stmt->execute()) {
                if($ids = $q->stmt->fetchAll(PDO::FETCH_COLUMN)) {
                    $c->where(array($this->classKey.'.id:IN'=>$ids));
                }
            }
        } elseif ($rules = $this->getProperty('rules')) {
            $c->select('id,pagetitle');
            $proMode =  $this->modx->getOption('seofilter_pro_mode',null,0,true);
            $q = $this->modx->newQuery('sfRule');
            $q->where(array('active'=>1));
            $q->select('page,pages');
            $ids = array();
            if ($q->prepare() && $q->stmt->execute()) {
                while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                    if($proMode) {
                        $pages = $row['pages'];
                        if(empty($pages)) {
                            $pages = $row['page'];
                        }
                    } else {
                        $pages = $row['page'];
                        if(empty($pages) && !empty($row['pages'])) {
                            $pages = $row['pages'];
                        }
                    }
                    $pages = array_map('trim',explode(',',$pages));
                    $ids = array_merge($ids,$pages);
                }
            }
            $ids = array_unique($ids);
            if($ids) {
                $c->where(array($this->classKey.'.id:IN'=>$ids));
            }
        } elseif ($this->getProperty('combo')) {
            $c->select('id,pagetitle');
        }
        $c->where(array('class_key:!=' => 'msProduct'));
        if ($id = (int)$this->getProperty('id')) {
            $c->where(array('id' => $id));
        }
        if ($query = trim($this->getProperty('query'))) {
            $c->where(array(
                'pagetitle:LIKE' => "%{$query}%",
                'OR:id' => $query
            ));
        }

        return $c;
    }
    /**
     * @param xPDOObject $object
     *
     * @return array
     */
    public function prepareRow(xPDOObject $object)
    {
        if ($this->getProperty('combo')) {
            $array = array(
                'id' => $object->get('id'),
                'pagetitle' => '(' . $object->get('id') . ') ' . $object->get('pagetitle'),
            );
        } else {
            $array = $object->toArray();
        }
        return $array;
    }
}
return 'msResourceGetListProcessor';