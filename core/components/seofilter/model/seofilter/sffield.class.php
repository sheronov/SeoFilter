<?php
class sfField extends xPDOSimpleObject {


    public function makeUrl($value = '') {
        $separator = $this->xpdo->getOption('seofilter_separator', null, '-', true);
        if(!$alias = $this->get('alias')) {
            $alias = $this->get('key');
        }
        if($this->get('hideparam')) {
            $url = $value;
        } elseif ($this->get('valuefirst')) {
            $url = $value.$separator.$alias;
        } else {
            $url = $alias.$separator.$value;
        }
        return $url;
    }

    public function getValueByInput($input = '',$class = '',$key='') {;
        $value = '';
        if(!$class)
            $class = $this->get('class');
        if(!$key)
            $key = $this->get('key');

        if ($class == 'msVendor') {
            $q = $this->xpdo->newQuery($class);
            $q->limit(1);
            $q->where(array('id'=>$input));
            $q->select('name');
            if ($q->prepare() && $q->stmt->execute()) {
                $value = $q->stmt->fetch(PDO::FETCH_COLUMN);
            }
        } else {
            if($this->get('xpdo')) {
                $xpdo_id = $this->get('xpdo_id');
                $xpdo_name = $this->get('xpdo_name');
                if($xpdo_class = $this->get('xpdo_class')) {
                    if($package = $this->get('xpdo_package')) {
                        $this->xpdo->addPackage($package, $this->xpdo->getOption('core_path').'components/'.$package.'/model/');
                    }
                    $q = $this->xpdo->newQuery($xpdo_class);
                    $q->where(array($xpdo_id=>$input));
                    $q->limit(1);
                    $q->select($xpdo_name);
                    if ($q->prepare() && $q->stmt->execute()) {
                        $value = $q->stmt->fetch(PDO::FETCH_COLUMN);
                    }
                }
            } else {
                $value = $input;
            }

        }
        return $value;
    }

    public function updateMask() {


        return true;
    }

}