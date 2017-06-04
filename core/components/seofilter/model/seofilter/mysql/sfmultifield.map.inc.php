<?php
$xpdo_meta_map['sfMultiField']= array (
  'package' => 'seofilter',
  'version' => '1.1',
  'table' => 'seofilter_multifield',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'name' => '',
    'page' => 0,
    'url' => '',
    'rank' => 0,
    'active' => 1,
  ),
  'fieldMeta' => 
  array (
    'name' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'page' => 
    array (
      'dbtype' => 'integer',
      'precision' => '10',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'url' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'rank' => 
    array (
      'dbtype' => 'integer',
      'precision' => '10',
      'phptype' => 'integer',
      'attributes' => 'unsigned',
      'null' => true,
      'default' => 0,
    ),
    'active' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'phptype' => 'boolean',
      'null' => true,
      'default' => 1,
    ),
  ),
  'indexes' => 
  array (
    'url' => 
    array (
      'alias' => 'url',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'url' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'page' => 
    array (
      'alias' => 'page',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'page' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'active' => 
    array (
      'alias' => 'active',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'active' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
  ),
  'composites' => 
  array (
    'Seo' => 
    array (
      'class' => 'sfSeoMeta',
      'local' => 'id',
      'foreign' => 'multi_id',
      'cardinality' => 'one',
      'owner' => 'local',
    ),
    'Link' => 
    array (
      'class' => 'sfFieldIds',
      'local' => 'id',
      'foreign' => 'multi_id',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'Url' => 
    array (
      'class' => 'sfUrls',
      'local' => 'id',
      'foreign' => 'multi_id',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
);
