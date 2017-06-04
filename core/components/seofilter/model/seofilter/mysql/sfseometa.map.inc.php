<?php
$xpdo_meta_map['sfSeoMeta']= array (
  'package' => 'seofilter',
  'version' => '1.1',
  'table' => 'seofilter_seometa',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'multi_id' => 0,
    'field_id' => 0,
    'name' => '',
    'title' => '',
    'h1' => '',
    'h2' => '',
    'introtext' => '',
    'description' => '',
    'text' => '',
    'content' => '',
    'rank' => 0,
    'active' => 1,
  ),
  'fieldMeta' => 
  array (
    'multi_id' => 
    array (
      'dbtype' => 'integer',
      'precision' => '10',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'field_id' => 
    array (
      'dbtype' => 'integer',
      'precision' => '10',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'name' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => true,
      'default' => '',
    ),
    'title' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => true,
      'default' => '',
    ),
    'h1' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => true,
      'default' => '',
    ),
    'h2' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => true,
      'default' => '',
    ),
    'introtext' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => true,
      'default' => '',
    ),
    'description' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => true,
      'default' => '',
    ),
    'text' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => true,
      'default' => '',
    ),
    'content' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => true,
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
    'multi_id' => 
    array (
      'alias' => 'multi_id',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'multi_id' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'field_id' => 
    array (
      'alias' => 'field_id',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'field_id' => 
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
  'aggregates' => 
  array (
    'Field' => 
    array (
      'class' => 'sfField',
      'local' => 'field_id',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'Multi' => 
    array (
      'class' => 'sfMultiField',
      'local' => 'multi_id',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
