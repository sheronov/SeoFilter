<?php
class sfField extends xPDOSimpleObject {

    /**
     * Returns translited word
     *
     * @param string $word
     * @param int $to_rus = 0
     *
     * @return string
     */
    public function translit($word = '', $to_rus = 0) {

        $translit = array(
            'а' => 'a',   'б' => 'b',   'в' => 'v',
            'г' => 'g',   'д' => 'd',   'е' => 'e',
            'ё' => 'yo',   'ж' => 'zh',  'з' => 'z',
            'и' => 'i',   'й' => 'j',   'к' => 'k',
            'л' => 'l',   'м' => 'm',   'н' => 'n',
            'о' => 'o',   'п' => 'p',   'р' => 'r',
            'с' => 's',   'т' => 't',   'у' => 'u',
            'ф' => 'f',   'х' => 'x',   'ц' => 'c',
            'ч' => 'ch',  'ш' => 'sh',  'щ' => 'shh',
            'ь' => 'y',  'ы' => 'uy',   'ъ' => '\'\'',
            'э' => 'ei',   'ю' => 'yu',  'я' => 'ya',
            'А' => 'A',   'Б' => 'B',   'В' => 'V',
            'Г' => 'G',   'Д' => 'D',   'Е' => 'E',
            'Ё' => 'YO',   'Ж' => 'Zh',  'З' => 'Z',
            'И' => 'I',   'Й' => 'J',   'К' => 'K',
            'Л' => 'L',   'М' => 'M',   'Н' => 'N',
            'О' => 'O',   'П' => 'P',   'Р' => 'R',
            'С' => 'S',   'Т' => 'T',   'У' => 'U',
            'Ф' => 'F',   'Х' => 'X',   'Ц' => 'C',
            'Ч' => 'CH',  'Ш' => 'SH',  'Щ' => 'SHH',
            'Ы' => 'UY',   'Ь' => 'Y',  'Ъ' => '\'\'',
            'Э' => 'EI',   'Ю' => 'YU',  'Я' => 'YA',
            ' ' => '-'
        );

        $translit = $to_rus == 1 ? array_flip($translit) : $translit;

        return mb_strtolower(strtr($word, $translit));
    }

    public function makeUrl($value = '') {
        $separator = $this->xpdo->getOption('seofilter_separator', null, '-', true);
        if(!$alias = $this->get('alias')) {
            $alias = $this->get('key');
        }
        if($this->get('hideparam')) {
            $url = $value;
        } else if ($this->get('valuefirst')) {
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

}