<?php

/* How to make new entries here for non-packaged languages:

-- 'code' should match the name of the directory for the language under \lang
.-- 'name' is the name that will be displayed in the language selection list (in Users and Display Setup)
-- 'rtl' only needs to be set for right-to-left languages like Arabic and Hebrew
-- 'encoding' used in translation file
-- 'version' always set to '' for manually installed languages.
-- 'path' installation path related to FA root (e.g. 'lang/en_US').
*/


$installed_languages = array (
  0 => 
  array (
    'code' => 'C',
    'name' => 'English',
    'encoding' => 'iso-8859-1',
  ),
 1 => 
  array (
    'name' => 'Arabic',
    'package' => 'ar_EG',
    'code' => 'ar_EG',
    'encoding' => 'utf-8',
    'version' => '2.3.12-1',
    'path' => 'lang/ar_EG',
    'rtl' => true,
  ),
  2 => 
  array (
    'name' => 'Urdu',
    'package' => 'ur_PK',
    'code' => 'ur_PK',
    'encoding' => 'utf-8',
    'version' => '2.4.1-3',
    'path' => 'lang/ur_PK',
    'rtl' => true,
  ),
  3 => 
  array (
    'name' => 'Chinese (Simplified)',
    'package' => 'zh_CN',
    'code' => 'zh_CN',
    'encoding' => 'utf-8',
    'version' => '2.4.1-3',
    'path' => 'lang/zh_CN',
  ),
  4 => 
  array (
    'name' => 'Chinese (Traditional)',
    'package' => 'zh_TW',
    'code' => 'zh_TW',
    'encoding' => 'utf-8',
    'version' => '2.4.1-3',
    'path' => 'lang/zh_TW',
  ),
);

$dflt_lang = 'C';
