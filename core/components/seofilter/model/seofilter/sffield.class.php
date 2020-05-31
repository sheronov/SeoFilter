<?php

class sfField extends xPDOSimpleObject
{


    public function makeUrl($value = '')
    {
        $separator = $this->xpdo->getOption('seofilter_separator', null, '-', true);
        if (!$alias = $this->get('alias')) {
            $alias = $this->get('key');
        }
        if (!$value) {
            $value = '{$'.$alias.'}';
        }
        if ($this->get('hideparam')) {
            $url = $value;
        } elseif ($this->get('valuefirst')) {
            $url = $value.$separator.$alias;
        } else {
            $url = $alias.$separator.$value;
        }
        return $url;
    }

    public function getValueByInput($input = '', $class = '', $key = '')
    {
        $value = '';
        if (!$class) {
            $class = $this->get('class');
        }
        if (!$key) {
            $key = $this->get('key');
        }

        if (mb_strtolower($class) === 'msvendor') {
            $q = $this->xpdo->newQuery($class);
            $q->limit(1);
            $q->where(['id' => $input]);
            $q->select('name');
            if ($q->prepare() && $q->stmt->execute()) {
                $value = $q->stmt->fetch(PDO::FETCH_COLUMN);
            }
        } elseif ($this->get('xpdo')) {
            $xpdo_id = $this->get('xpdo_id');
            $xpdo_name = $this->get('xpdo_name');
            if ($xpdo_class = $this->get('xpdo_class')) {
                if ($package = $this->get('xpdo_package')) {
                    $this->xpdo->addPackage(strtolower($package),
                        $this->xpdo->getOption('core_path').'components/'.strtolower($package).'/model/');
                }
                $q = $this->xpdo->newQuery($xpdo_class);
                $q->where([$xpdo_id => $input]);
                $q->limit(1);
                if ($this->get('relation')) {
                    $relation_field = $this->get('relation_field');
                    $relation_column = $this->get('relation_column');
                    if ($relation_column) {
                        $q->select($xpdo_name.','.$relation_column);
                        if ($q->prepare() && $q->stmt->execute()) {
                            if ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                                $value = ['value' => $row[$xpdo_name], 'relation' => $row[$relation_column]];
                            }
                        }
                    } else {
                        $q->select($xpdo_name);
                        if ($q->prepare() && $q->stmt->execute()) {
                            $value = $q->stmt->fetch(PDO::FETCH_COLUMN);
                        }
                    }
                } else {
                    $q->select($xpdo_name);
                    if ($q->prepare() && $q->stmt->execute()) {
                        $value = $q->stmt->fetch(PDO::FETCH_COLUMN);
                    }
                }
            }
        } else {
            $value = $input;
        }

        return $value;
    }

    public function updateMask()
    {
        return true;
    }

}