<?php

/*

Connection Information for the database
$def_coy - the default company that is pre-selected on login

'host' - the computer ip address or name where the database is. The default is 'localhost' assuming that the web server is also the sql server.

'dbuser' - the user name under which the company database should be accessed.
  NB it is not secure to use root as the dbuser with no password - a user with appropriate privileges must be set up.

'dbpassword' - the password required for the dbuser to authorise the above database user.

'dbname' - the name of the database as defined in the RDMS being used. Typically RDMS allow many databases to be maintained under the same server.
'tbpref' - prefix on table names, or '' if not used. Always use non-empty prefixes if multiply company use the same database.

*/


$def_coy = 0;

$tb_pref_counter = 192;

$db_connections = array (
  0 => 
  array (
    'name' => 'SMAC',
    'host' => 'localhost',
    'dbname' => 'cloudso1_smac',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_smac',
    'dbpassword' => 'myz47m',
  ),
 1 => 
  array (
    'name' => 'HOC',
    'host' => 'localhost',
    'dbname' => 'cloudso1_hoc',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_hoc',
    'dbpassword' => 'myz47m',
  ),
 2 => 
  array (
    'name' => 'AIPL',
    'host' => 'localhost',
    'dbname' => 'cloudso1_aipl',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_aipl',
    'dbpassword' => 'myz47m',
  ),
  3 => 
  array (
    'name' => 'THERMOPLAS',
    'host' => 'localhost',
    'dbname' => 'cloudso1_thermoplas',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_thermoplas',
    'dbpassword' => 'myz47m',
  ),
4 => 
  array (
    'name' => 'SKF',
    'host' => 'localhost',
    'dbname' => 'cloudso1_sakuf',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_sakuf',
    'dbpassword' => 'myz47m',
  ),
5 => 
  array (
    'name' => 'FLC',
    'host' => 'localhost',
    'dbname' => 'cloudso1_flc',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_flc',
    'dbpassword' => 'myz47m',
  ),
6 => 
  array (
    'name' => 'KBS',
    'host' => 'localhost',
    'dbname' => 'cloudso1_kbs',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_kbs',
    'dbpassword' => 'myz47m',
  ),
 7 =>
  array (
    'name' => 'BNC',
    'host' => 'localhost',
    'dbname' => 'cloudso1_bnc',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_bnc',
    'dbpassword' => 'myz47m',
  ),
8 => 
  array (
    'name' => 'DEMO',
    'host' => 'localhost',
    'dbname' => 'cloudso1_demo',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_demo',
    'dbpassword' => 'myz47m',
  ),
9 => 
  array (
    'name' => 'AOIS',
    'host' => 'localhost',
    'dbname' => 'cloudso1_aois',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_aois',
    'dbpassword' => 'myz47m',
  ),
10 => 
  array (
    'name' => 'ASHAROT',
    'host' => 'localhost',
    'dbname' => 'cloudso1_asharot',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_asharot',
    'dbpassword' => 'myz47m',
  ),
11 =>
  array (
    'name' => 'AKR',
    'host' => 'localhost',
    'dbname' => 'cloudso1_akr',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_akr',
    'dbpassword' => 'myz47m',
  ),
12 =>
  array (
    'name' => 'BNT',
    'host' => 'localhost',
    'dbname' => 'cloudso1_bnt',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_bnt',
    'dbpassword' => 'myz47m',
  ),
 13 => 
  array (
    'name' => 'UC',
    'host' => 'localhost',
    'dbname' => 'cloudso1_uc',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_uc',
    'dbpassword' => 'myz47m',
  ),
14 =>
  array (
    'name' => 'NKR',
    'host' => 'localhost',
    'dbname' => 'cloudso1_nkr',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_nkr',
    'dbpassword' => 'myz47m',
  ),
    15 => 
  array (
    'name' => 'STECH',
    'host' => 'localhost',
    'dbname' => 'cloudso1_stech',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_stech',
    'dbpassword' => 'myz47m',
  ),
    16 => 
  array (
    'name' => 'LAKHANIGLASS',
    'host' => 'localhost',
    'dbname' => 'cloudso1_lakhaniglass',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_lakhaniglass',
    'dbpassword' => 'myz47m',
  ),
  17 => 
  array (
    'name' => 'WSL',
    'host' => 'localhost',
    'dbname' => 'cloudso1_wsl',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_wsl',
    'dbpassword' => 'myz47m',
  ),
  18 => 
  array (
    'name' => 'JASCO',
    'host' => 'localhost',
    'dbname' => 'cloudso1_jasco',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_jasco',
    'dbpassword' => 'myz47m',
  ),
    19 => 
  array (
    'name' => 'WTRUCK',
    'host' => 'localhost',
    'dbname' => 'cloudso1_wtruck',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_wtruck',
    'dbpassword' => 'myz47m',
  ),
    20 => 
  array (
    'name' => 'KAUSAR',
    'host' => 'localhost',
    'dbname' => 'cloudso1_kausar',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_kausar',
    'dbpassword' => 'myz47m',
  ),
    21 => 
  array (
    'name' => 'ATQC',
    'host' => 'localhost',
    'dbname' => 'cloudso1_atqc',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_atqc',
    'dbpassword' => 'myz47m',
  ),
    22 => 
  array (
    'name' => 'CHI',
    'host' => 'localhost',
    'dbname' => 'cloudso1_chi',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_chi',
    'dbpassword' => 'myz47m',
  ),  
  23 => 
  array (
    'name' => 'PTI',
    'host' => 'localhost',
    'dbname' => 'cloudso1_pti',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_pti',
    'dbpassword' => 'myz47m',
  ), 
   24 => 
  array (
    'name' => 'MEIOLD',
    'host' => 'localhost',
    'dbname' => 'cloudso1_mei',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_mei',
    'dbpassword' => 'myz47m',
  ), 
   25 => 
  array (
    'name' => 'AHN',
    'host' => 'localhost',
    'dbname' => 'cloudso1_ahn',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_ahn',
    'dbpassword' => 'myz47m',
  ),
    26 => 
  array (
    'name' => 'RPL2',
    'host' => 'localhost',
    'dbname' => 'cloudso1_rpl2',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_rpl2',
    'dbpassword' => 'myz47m',
  ),  
  27 => 
  array (
    'name' => 'HOCPL',
    'host' => 'localhost',
    'dbname' => 'cloudso1_hoc2',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_hoc2',
    'dbpassword' => 'myz47m',
  ),
  28 => 
  array (
    'name' => 'MTG',
    'host' => 'localhost',
    'dbname' => 'cloudso1_mtg',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_mtg',
    'dbpassword' => 'myz47m',
  ),
  29 => 
  array (
    'name' => 'COMP1',
    'host' => 'localhost',
    'dbname' => 'cloudso1_comp1',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_comp1',
    'dbpassword' => 'myz47m',
  ),
  30 => 
  array (
    'name' => 'IMEC',
    'host' => 'localhost',
    'dbname' => 'cloudso1_imec',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_imec',
    'dbpassword' => 'myz47m',
  ),
  31 => 
  array (
    'name' => 'MEHMOOD',
    'host' => 'localhost',
    'dbname' => 'cloudso1_mehmood',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_mehmood',
    'dbpassword' => 'myz47m',
  ),
  32 => 
  array (
    'name' => 'SES',
    'host' => 'localhost',
    'dbname' => 'cloudso1_ses',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_ses',
    'dbpassword' => 'myz47m',
  ),
  33 => 
  array (
    'name' => 'KTRD',
    'host' => 'localhost',
    'dbname' => 'cloudso1_ktrd',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_ktrd',
    'dbpassword' => 'myz47m',
  ),  
  34 => 
  array (
    'name' => '3D',
    'host' => 'localhost',
    'dbname' => 'cloudso1_3d',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_3d',
    'dbpassword' => 'myz47m',
  ), 
  35 => 
  array (
    'name' => 'ABC',
    'host' => 'localhost',
    'dbname' => 'cloudso1_abc',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_abc',
    'dbpassword' => 'myz47m',
  ),   
   36 => 
  array (
    'name' => 'ZOT',
    'host' => 'localhost',
    'dbname' => 'cloudso1_zot',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_zot',
    'dbpassword' => 'myz47m',
  ),   
  37 => 
  array (
    'name' => 'LBH',
    'host' => 'localhost',
    'dbname' => 'cloudso1_lbh',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_lbh',
    'dbpassword' => 'myz47m',
  ), 
  38 => 
  array (
    'name' => 'ICTC',
    'host' => 'localhost',
    'dbname' => 'cloudso1_ictc',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_ictc',
    'dbpassword' => 'myz47m',
  ),   
  39 => 
  array (
    'name' => 'LGC',
    'host' => 'localhost',
    'dbname' => 'cloudso1_lgc',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_lgc',
    'dbpassword' => 'myz47m',
  ),   
  40 => 
  array (
    'name' => 'ALA',
    'host' => 'localhost',
    'dbname' => 'cloudso1_ala',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_ala',
    'dbpassword' => 'myz47m',
  ),     
  41 => 
  array (
    'name' => 'ELECTROMART',
    'host' => 'localhost',
    'dbname' => 'cloudso1_em',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_em',
    'dbpassword' => 'myz47m',
  ),
  42 => 
  array (
    'name' => 'WFI',
    'host' => 'localhost',
    'dbname' => 'cloudso1_wfi',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_wfi',
    'dbpassword' => 'myz47m',
  ),
  43 => 
  array (
    'name' => 'MMGT',
    'host' => 'localhost',
    'dbname' => 'cloudso1_mmgt',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_mmgt',
    'dbpassword' => 'myz47m',
  ),
  44 => 
  array (
    'name' => 'MIT',
    'host' => 'localhost',
    'dbname' => 'cloudso1_mit',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_mit',
    'dbpassword' => 'myz47m',
  ),
   45 => 
  array (
    'name' => 'MAYA',
    'host' => 'localhost',
    'dbname' => 'cloudso1_maya',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_maya',
    'dbpassword' => 'myz47m',
  ),
  46 => 
  array (
    'name' => 'UIX',
    'host' => 'localhost',
    'dbname' => 'cloudso1_uix',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_uix',
    'dbpassword' => 'myz47m',
  ),
  47 => 
  array (
    'name' => 'DINAR',
    'host' => 'localhost',
    'dbname' => 'cloudso1_dinar',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_dinar',
    'dbpassword' => 'myz47m',
  ),
  48 => 
  array (
    'name' => 'MUNAF',
    'host' => 'localhost',
    'dbname' => 'cloudso1_munaf',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_munaf',
    'dbpassword' => 'myz47m',
  ),
  49 => 
  array (
    'name' => 'MUSLIM',
    'host' => 'localhost',
    'dbname' => 'cloudso1_musl',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_musl',
    'dbpassword' => 'myz47m',
  ),
  50 => 
  array (
    'name' => 'FIDA',
    'host' => 'localhost',
    'dbname' => 'cloudso1_fida',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_fida',
    'dbpassword' => 'myz47m',
  ),
  51 => 
  array (
    'name' => 'CM',
    'host' => 'localhost',
    'dbname' => 'cloudso1_cm',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_cm',
    'dbpassword' => 'myz47m',
  ),
  52 => 
  array (
    'name' => 'MARCO',
    'host' => 'localhost',
    'dbname' => 'cloudso1_marco',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_marco',
    'dbpassword' => 'myz47m',
  ),
   53 => 
  array (
    'name' => 'ALG',
    'host' => 'localhost',
    'dbname' => 'cloudso1_alg',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_alg',
    'dbpassword' => 'myz47m',
  ),
   54 => 
  array (
    'name' => 'IYSH',
    'host' => 'localhost',
    'dbname' => 'cloudso1_iysh',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_iysh',
    'dbpassword' => 'myz47m',
  ),
   55 => 
  array (
    'name' => 'SRC',
    'host' => 'localhost',
    'dbname' => 'cloudso1_src',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_src',
    'dbpassword' => 'myz47m',
  ),
   56 => 
  array (
    'name' => 'AMAC',
    'host' => 'localhost',
    'dbname' => 'cloudso1_amac',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_amac',
    'dbpassword' => 'myz47m',
  ),
   57 => 
  array (
    'name' => 'COLOURS',
    'host' => 'localhost',
    'dbname' => 'cloudso1_colours',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_colours',
    'dbpassword' => 'myz47m',
  ),
   58 => 
  array (
    'name' => 'RPLTESTING',
    'host' => 'localhost',
    'dbname' => 'cloudso1_rpltesting',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_rpltesting',
    'dbpassword' => 'myz47m',
  ),
   59 => 
  array (
    'name' => 'TURFAH',
    'host' => 'localhost',
    'dbname' => 'cloudso1_turfah',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_turfah',
    'dbpassword' => 'myz47m',
  ),
    60 => 
  array (
    'name' => 'AMAN',
    'host' => 'localhost',
    'dbname' => 'cloudso1_aman',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_aman',
    'dbpassword' => 'myz47m',
  ),
     61 => 
  array (
    'name' => 'IMVPP',
    'host' => 'localhost',
    'dbname' => 'cloudso1_imvpp',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_imvpp',
    'dbpassword' => 'myz47m',
  ),
      62 => 
  array (
    'name' => 'XPLORE',
    'host' => 'localhost',
    'dbname' => 'cloudso1_xplore',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_xplore',
    'dbpassword' => 'myz47m',
  ),
     63 => 
  array (
    'name' => 'RE',
    'host' => 'localhost',
    'dbname' => 'cloudso1_re',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_re',
    'dbpassword' => 'myz47m',
  ),
   64 => 
  array (
    'name' => 'MAHENTI',
    'host' => 'localhost',
    'dbname' => 'cloudso1_mahenti',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_mahenti',
    'dbpassword' => 'myz47m',
  ),
   65 => 
  array (
    'name' => 'RS',
    'host' => 'localhost',
    'dbname' => 'cloudso1_rs',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_rs',
    'dbpassword' => 'myz47m',
  ),
   66 => 
  array (
    'name' => 'SHAMIM',
    'host' => 'localhost',
    'dbname' => 'cloudso1_shamim',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_shamim',
    'dbpassword' => 'myz47m',
  ),
    67 =>
  array (
    'name' => 'FAISAL',
    'host' => 'localhost',
    'dbname' => 'cloudso1_faisal',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_faisal',
    'dbpassword' => 'myz47m',
  ),
    68 => 
  array (
    'name' => 'CB',
    'host' => 'localhost',
    'dbname' => 'cloudso1_cb',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_cb',
    'dbpassword' => 'myz47m',
  ),
   69 => 
  array (
    'name' => 'AMACCOM',
    'host' => 'localhost',
    'dbname' => 'cloudso1_amactax',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_amactax',
    'dbpassword' => 'myz47m',
  ),
  70 => 
  array (
    'name' => 'FDP',
    'host' => 'localhost',
    'dbname' => 'cloudso1_fdp',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_fdp',
    'dbpassword' => 'myz47m',
  ),
  71 => 
  array (
    'name' => 'VPS',
    'host' => 'localhost',
    'dbname' => 'cloudso1_vps',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_vps',
    'dbpassword' => 'myz47m',
  ),
  72 => 
  array (
    'name' => 'BNW',
    'host' => 'localhost',
    'dbname' => 'cloudso1_bnw',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_bnw',
    'dbpassword' => 'myz47m',
  ),
  73 => 
  array (
    'name' => 'SNI',
    'host' => '103.227.176.17',
    'dbname' => 'hisaabne_sales3ssni',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'hisaabne_sales3ssni',
    'dbpassword' => 'myz47m',
    
  ), 
   74 => 
  array (
    'name' => 'MACORP',
    'host' => 'localhost',
    'dbname' => 'cloudso1_macorp',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_macorp',
    'dbpassword' => 'myz47m',
  ),
   75 => 
  array (
    'name' => 'EURO',
    'host' => 'localhost',
    'dbname' => 'cloudso1_euro',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_euro',
    'dbpassword' => 'myz47m',
  ),
  76 => 
  array (
    'name' => 'PETPAL',
    'host' => 'localhost',
    'dbname' => 'cloudso1_petpal',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_petpal',
    'dbpassword' => 'myz47m',
  ),
  77 => 
  array (
    'name' => 'KEVLAAR',
    'host' => 'localhost',
    'dbname' => 'cloudso1_kevlar',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_kevlar',
    'dbpassword' => 'myz47m',
  ),
 78 => 
  array (
    'name' => 'MUSLIM2',
    'host' => 'localhost',
    'dbname' => 'cloudso1_musl2',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_musl2',
    'dbpassword' => 'myz47m',
  ),
  79 =>
  array (
    'name' => 'BNT2',
    'host' => 'localhost',
    'dbname' => 'cloudso1_bnt2',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_bnt2',
    'dbpassword' => 'myz47m',
  ),
  80 => 
  array (
    'name' => 'PS',
    'host' => 'localhost',
    'dbname' => 'cloudso1_ps',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_ps',
    'dbpassword' => 'myz47m',
  ),
  81 => 
  array (
    'name' => 'ZYMAL',
    'host' => 'localhost',
    'dbname' => 'cloudso1_zymal',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_zymal',
    'dbpassword' => 'myz47m',
  ),
  82 => 
  array (
    'name' => 'UCC',
    'host' => 'localhost',
    'dbname' => 'cloudso1_ucc',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_ucc',
    'dbpassword' => 'myz47m',
  ),
  83 => 
  array (
    'name' => 'VIZ',
    'host' => 'localhost',
    'dbname' => 'cloudso1_viz',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_viz',
    'dbpassword' => 'myz47m',
  ),
  84 => 
  array (
    'name' => 'AHP',
    'host' => 'localhost',
    'dbname' => 'cloudso1_ahp',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_ahp',
    'dbpassword' => 'myz47m',
  ),
  85 => 
  array (
    'name' => 'GAUR',
    'host' => 'localhost',
    'dbname' => 'cloudso1_gaur',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_gaur',
    'dbpassword' => 'myz47m',
  ),
  86 => 
  array (
    'name' => 'BACKCHECK',
    'host' => 'localhost',
    'dbname' => 'cloudso1_backcheck',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_backchk',
    'dbpassword' => 'myz47m',
  ),
   87 => 
  array (
    'name' => 'JUNCTIONZ',
    'host' => 'localhost',
    'dbname' => 'cloudso1_junctionz',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_junctionz',
    'dbpassword' => 'myz47m',
  ),
  88 => 
  array (
    'name' => 'TECHNOSYNC',
    'host' => 'localhost',
    'dbname' => 'cloudso1_technosync',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_techsyn',
    'dbpassword' => 'myz47m',
  ),
  89 => 
  array (
    'name' => 'MODIANT',
    'host' => 'localhost',
    'dbname' => 'cloudso1_modiant',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_modiant',
    'dbpassword' => 'myz47m',
  ),
  90 => 
  array (
    'name' => 'DSSLS',
    'host' => 'localhost',
    'dbname' => 'cloudso1_dssls',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_dssls',
    'dbpassword' => 'myz47m',
  ),
  91 => 
  array (
    'name' => 'MOON',
    'host' => 'localhost',
    'dbname' => 'cloudso1_moon',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_moon',
    'dbpassword' => 'myz47m',
  ),
  92 => 
  array (
    'name' => 'BIZCLOUD',
    'host' => 'localhost',
    'dbname' => 'cloudso1_bizcloud',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_bzcloud',
    'dbpassword' => 'myz47m',
  ),
  93 => 
  array (
    'name' => 'PREMIUM',
    'host' => 'localhost',
    'dbname' => 'cloudso1_premiumm',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_premium',
    'dbpassword' => 'myz47m',
  ),
   94 => 
  array (
    'name' => 'SPINTEX',
    'host' => 'localhost',
    'dbname' => 'cloudso1_spintex',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_spintex',
    'dbpassword' => 'myz47m',
  ),
   95 => 
  array (
    'name' => 'BALOCH',
    'host' => 'localhost',
    'dbname' => 'cloudso1_baloch',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_baloch',
    'dbpassword' => 'myz47m',
  ),
  96 => 
  array (
    'name' => 'SACAHO',
    'host' => 'localhost',
    'dbname' => 'cloudso1_sacaho',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_sacaho',
    'dbpassword' => 'myz47m',
  ),
  97 => 
  array (
    'name' => 'DYS',
    'host' => 'localhost',
    'dbname' => 'cloudso1_dys',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_dys',
    'dbpassword' => 'myz47m',
  ),
  98 => 
  array (
    'name' => 'CTRD',
    'host' => 'localhost',
    'dbname' => 'cloudso1_ctrd',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_ctrd',
    'dbpassword' => 'myz47m',
  ),
    99 => 
  array (
    'name' => 'SAP',
    'host' => 'localhost',
    'dbname' => 'cloudso1_sap',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_sap',
    'dbpassword' => 'myz47m',
  ),
  100 => 
  array (
    'name' => 'ENTERPRISE',
    'host' => 'localhost',
    'dbname' => 'cloudso1_enterprise',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_entrprs',
    'dbpassword' => 'myz47m',
  ),
  101 => 
  array (
    'name' => 'RMS',
    'host' => 'localhost',
    'dbname' => 'cloudso1_rms',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_rms',
    'dbpassword' => 'myz47m',
  ),
  102 => 
  array (
    'name' => 'POS',
    'host' => 'localhost',
    'dbname' => 'cloudso1_pos',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_pos',
    'dbpassword' => 'myz47m',
  ),
  103 => 
  array (
    'name' => 'BN',
    'host' => 'localhost',
    'dbname' => 'cloudso1_bn',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_bn',
    'dbpassword' => 'myz47m',
  ),
  104 => 
  array (
    'name' => 'NOMI',
    'host' => 'localhost',
    'dbname' => 'cloudso1_nomi',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_nomi',
    'dbpassword' => 'myz47m',
  ),
  105 => 
  array (
    'name' => 'DMNWS',
    'host' => 'localhost',
    'dbname' => 'cloudso1_dmnws',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_dmnws',
    'dbpassword' => 'myz47m',
  ),
  106 => 
  array (
    'name' => 'BURHAN',
    'host' => 'localhost',
    'dbname' => 'cloudso1_burhan',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_burhan',
    'dbpassword' => 'myz47m',
  ),
  107 => 
  array (
    'name' => 'TP',
    'host' => 'localhost',
    'dbname' => 'cloudso1_tp',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_tp',
    'dbpassword' => 'myz47m',
  ),
  108 => 
  array (
    'name' => 'OPTISURGE',
    'host' => 'localhost',
    'dbname' => 'cloudso1_optisurge',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_optisrg',
    'dbpassword' => 'myz47m',
  ),
    109 => 
  array (
    'name' => 'RMSALT',
    'host' => 'localhost',
    'dbname' => 'cloudso1_rmsalt',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_rmsalt',
    'dbpassword' => 'myz47m',
  ),
  110 => 
  array (
    'name' => 'GIGI',
    'host' => 'localhost',
    'dbname' => 'cloudso1_gigi',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_gigi',
    'dbpassword' => 'myz47m',
  ),
  111 => 
  array (
    'name' => 'BOOKMART',
    'host' => 'localhost',
    'dbname' => 'cloudso1_bookmart',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_bookmrt',
    'dbpassword' => 'myz47m',
  ),
   112 => 
  array (
    'name' => 'SHAUKAT',
    'host' => 'localhost',
    'dbname' => 'cloudso1_shaukat',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_shaukat',
    'dbpassword' => 'myz47m',
  ),
  113 =>
  array (
    'name' => 'BNTTAX',
    'host' => 'localhost',
    'dbname' => 'cloudso1_bnt2',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_bnt2',
    'dbpassword' => 'myz47m',
  ),
  114 => 
  array (
    'name' => 'LUBRIZONE',
    'host' => 'localhost',
    'dbname' => 'cloudso1_lubrizone',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_lz',
    'dbpassword' => 'myz47m',
  ),
  115 => 
  array (
    'name' => 'KINEMATIC',
    'host' => 'localhost',
    'dbname' => 'cloudso1_kinematic',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_kinmatc',
    'dbpassword' => 'myz47m',
  ),
  116 => 
  array (
    'name' => 'ALI',
    'host' => 'localhost',
    'dbname' => 'cloudso1_ali',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_ali',
    'dbpassword' => 'myz47m',
  ),
  117 => 
  array (
    'name' => 'KK',
    'host' => 'localhost',
    'dbname' => 'cloudso1_kk',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_kk',
    'dbpassword' => 'myz47m',
  ),
  118 => 
  array (
    'name' => 'ARKISH',
    'host' => 'localhost',
    'dbname' => 'cloudso1_arkish',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_arkish',
    'dbpassword' => 'myz47m',
  ),
     119 => 
  array (
    'name' => 'INDUS',
    'host' => 'localhost',
    'dbname' => 'cloudso1_indus',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_indus',
    'dbpassword' => 'myz47m',
  ),
  120 => 
  array (
    'name' => 'NEWTON',
    'host' => 'localhost',
    'dbname' => 'cloudso1_newton',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_newton',
    'dbpassword' => 'myz47m',
  ),
    121 => 
  array (
    'name' => 'CHEMCOS',
    'host' => 'localhost',
    'dbname' => 'cloudso1_chemcos',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_chemcos',
    'dbpassword' => 'myz47m',
  ),
   122 => 
  array (
    'name' => 'BIZMATIC',
    'host' => 'localhost',
    'dbname' => 'cloudso1_bizmatic',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_bzmatic',
    'dbpassword' => 'myz47m',
  ),
   123 => 
  array (
    'name' => 'PAKDUKAAN',
    'host' => 'localhost',
    'dbname' => 'cloudso1_pakdukaan',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_pkdukan',
    'dbpassword' => 'myz47m',
  ),
  124 => 
  array (
    'name' => 'CW',
    'host' => 'localhost',
    'dbname' => 'cloudso1_cw',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_cw',
    'dbpassword' => 'myz47m',
  ),
    125 => 
  array (
    'name' => 'SHAUKAT2',
    'host' => 'localhost',
    'dbname' => 'cloudso1_shaukat2',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_shaukt2',
    'dbpassword' => 'myz47m',
  ),
    126 => 
  array (
    'name' => 'KMISTRY',
    'host' => 'localhost',
    'dbname' => 'cloudso1_kmistry',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_kmistry',
    'dbpassword' => 'myz47m',
  ),
  127 => 
  array (
    'name' => 'RPLT',
    'host' => 'localhost',
    'dbname' => 'cloudso1_rpl2',
    'collation' => 'utf8_xx',
    'tbpref' => '1_',
    'dbuser' => 'cloudso1_rpl2',
    'dbpassword' => 'myz47m',
  ), 
  128 => 
  array (
    'name' => 'AAT',
    'host' => 'localhost',
    'dbname' => 'cloudso1_aat',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_aat',
    'dbpassword' => 'myz47m',
  ),
  129 => 
  array (
    'name' => 'CB2',
    'host' => 'localhost',
    'dbname' => 'cloudso1_cb_2',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_cb_2',
    'dbpassword' => 'myz47m',
  ),
  130 => 
  array (
    'name' => 'PLATOON',
    'host' => 'localhost',
    'dbname' => 'cloudso1_platoon',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_platoon',
    'dbpassword' => 'myz47m',
  ),
  131 => 
  array (
    'name' => 'AMI',
    'host' => 'localhost',
    'dbname' => 'cloudso1_ami',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_ami',
    'dbpassword' => 'myz47m',
  ),
  132 => 
  array (
    'name' => 'HASSAN',
    'host' => 'localhost',
    'dbname' => 'cloudso1_hassan',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_hassan',
    'dbpassword' => 'myz47m',
  ),
  133 => 
  array (
    'name' => 'BJS',
    'host' => 'localhost',
    'dbname' => 'cloudso1_bjs',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_bjs',
    'dbpassword' => 'myz47m',
  ),
  134 => 
  array (
    'name' => 'CMSS',
    'host' => 'localhost',
    'dbname' => 'cloudso1_cmss',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_cmss',
    'dbpassword' => 'myz47m',
  ),
  135 => 
  array (
    'name' => 'RPLM',
    'host' => 'localhost',
    'dbname' => 'cloudso1_rplm',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_rplm',
    'dbpassword' => 'myz47m',
  ),
  136 => 
  array (
    'name' => 'DATA',
    'host' => 'localhost',
    'dbname' => 'cloudso1_data',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_data',
    'dbpassword' => 'myz47m',
  ),
  137 => 
  array (
    'name' => 'FSTYLE',
    'host' => 'localhost',
    'dbname' => 'cloudso1_fstyle',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_fstyle',
    'dbpassword' => 'myz47m',
  ),
  138 => 
  array (
    'name' => 'EUROTAX',
    'host' => 'localhost',
    'dbname' => 'cloudso1_euro',
    'collation' => 'utf8_xx',
    'tbpref' => '1_',
    'dbuser' => 'cloudso1_euro',
    'dbpassword' => 'myz47m',
  ),
  139 => 
  array (
    'name' => 'SUNLEY',
    'host' => 'localhost',
    'dbname' => 'cloudso1_sunley',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_sunley',
    'dbpassword' => 'myz47m',
  ),
  140 => 
  array (
    'name' => 'PCHF',
    'host' => 'localhost',
    'dbname' => 'cloudso1_pchf',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_pchf',
    'dbpassword' => 'myz47m',
  ),
  141 => 
  array (
    'name' => 'MBP',
    'host' => 'localhost',
    'dbname' => 'cloudso1_mbp',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_mbp',
    'dbpassword' => 'myz47m',
  ),
    142 => 
  array (
    'name' => 'HAIRCLUB',
    'host' => 'localhost',
    'dbname' => 'cloudso1_hairclub',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_hairclub',
    'dbpassword' => 'myz47m',
  ),
  143 => 
  array (
    'name' => 'ASHARUK',
    'host' => 'localhost',
    'dbname' => 'cloudso1_asharuk',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_asharuk',
    'dbpassword' => 'myz47m',
  ),
   144 => 
  array (
    'name' => 'CHEMCOS2',
    'host' => 'localhost',
    'dbname' => 'cloudso1_chemcos2',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_chemcos2',
    'dbpassword' => 'myz47m',
  ),
  145 => 
  array (
    'name' => 'ASHARDB',
    'host' => 'localhost',
    'dbname' => 'cloudso1_ashardb',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_ashardb',
    'dbpassword' => 'myz47m',
  ),
   146 => 
  array (
    'name' => 'GRATON',
    'host' => 'localhost',
    'dbname' => 'cloudso1_graton',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_graton',
    'dbpassword' => 'myz47m',
  ),
  147 => 
  array (
    'name' => 'LZCENTRAL',
    'host' => 'localhost',
    'dbname' => 'cloudso1_lzcentral',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_lzcentral',
    'dbpassword' => 'myz47m',
  ),
   148 => 
  array (
    'name' => 'VIGOR',
    'host' => 'localhost',
    'dbname' => 'cloudso1_vigor',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_vigor',
    'dbpassword' => 'myz47m',
  ),
   149 => 
  array (
    'name' => 'ELECTROMECHANICA',
    'host' => 'localhost',
    'dbname' => 'cloudso1_electromech',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_electromech',
    'dbpassword' => 'myz47m',
  ),
   150 => 
  array (
    'name' => 'OZONE',
    'host' => 'localhost',
    'dbname' => 'cloudso1_ozone',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_ozone',
    'dbpassword' => 'myz47m',
  ),
  151 => 
  array (
    'name' => 'FINESTAR ',
    'host' => 'localhost',
    'dbname' => 'cloudso1_finestar',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_finestar',
    'dbpassword' => 'myz47m',
  ),
  152 => 
  array (
    'name' => 'TYRESALES',
    'host' => 'localhost',
    'dbname' => 'cloudso1_tyresales',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_tyresales',
    'dbpassword' => 'myz47m',
  ),
  153 => 
  array (
    'name' => 'MAXITECH',
    'host' => 'localhost',
    'dbname' => 'cloudso1_maxitech',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_maxitch',
    'dbpassword' => 'myz47m',
  ),
    154 => 
  array (
    'name' => 'PAKASIA',
    'host' => 'localhost',
    'dbname' => 'cloudso1_pakasia',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_pakasia',
    'dbpassword' => 'myz47m',
  ),
   155 => 
  array (
    'name' => 'PAKSTAR',
    'host' => 'localhost',
    'dbname' => 'cloudso1_pakstar',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_pakstar',
    'dbpassword' => 'myz47m',
  ),
   156 => 
  array (
    'name' => 'MEDISURG',
    'host' => 'localhost',
    'dbname' => 'cloudso1_medisurg',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_medisurg',
    'dbpassword' => 'myz47m',
  ),
  157 => 
  array (
    'name' => 'ASIF',
    'host' => 'localhost',
    'dbname' => 'cloudso1_asif',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_asif',
    'dbpassword' => 'myz47m',
  ),
  158 => 
  array (
    'name' => 'FMNPK',
    'host' => 'localhost',
    'dbname' => 'cloudso1_fmnpk',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_fmnpk',
    'dbpassword' => 'myz47m',
  ),
  159 => 
  array (
    'name' => 'MM',
    'host' => 'localhost',
    'dbname' => 'cloudso1_mm',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_mm',
    'dbpassword' => 'myz47m',
  ),
  160 => 
  array (
    'name' => 'SMRC',
    'host' => 'localhost',
    'dbname' => 'cloudso1_smrc',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_smrc',
    'dbpassword' => 'myz47m',
  ),
  161 => 
  array (
    'name' => 'LZNORTH',
    'host' => 'localhost',
    'dbname' => 'cloudso1_lznorth',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_lznorth',
    'dbpassword' => 'myz47m',
  ),
  162 => 
  array (
    'name' => 'ELECTROLINE',
    'host' => 'localhost',
    'dbname' => 'cloudso1_electroline',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_electroline',
    'dbpassword' => 'myz47m',
  ),
  163 => 
  array (
    'name' => 'CASAMIA',
    'host' => 'localhost',
    'dbname' => 'cloudso1_casamia',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_casamia',
    'dbpassword' => 'myz47m',
  ),
   164 => 
  array (
    'name' => 'SALAT',
    'host' => 'localhost',
    'dbname' => 'cloudso1_salat',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_salat',
    'dbpassword' => 'myz47m',
  ),
  165 => 
  array (
    'name' => 'ASHARAPL',
    'host' => 'localhost',
    'dbname' => 'cloudso1_asharapl',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_asharapl',
    'dbpassword' => 'myz47m',
  ),
  166 => 
  array (
    'name' => 'AXEN',
    'host' => 'localhost',
    'dbname' => 'cloudso1_axen',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_axen',
    'dbpassword' => 'myz47m',
  ),
   167 => 
  array (
    'name' => 'VETZ',
    'host' => 'localhost',
    'dbname' => 'cloudso1_vetz',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_vetz',
    'dbpassword' => 'myz47m',
  ),
    168 => 
  array (
    'name' => 'ITMEDVISION',
    'host' => 'localhost',
    'dbname' => 'cloudso1_itmedvision',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_itmedvision',
    'dbpassword' => 'myz47m',
  ),
    169 => 
  array (
    'name' => 'ELECTRA',
    'host' => 'localhost',
    'dbname' => 'cloudso1_electra',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_electra',
    'dbpassword' => 'myz47m',
  ),
   170 => 
  array (
    'name' => 'ROGHAY',
    'host' => 'localhost',
    'dbname' => 'cloudso1_roghay',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_roghay',
    'dbpassword' => 'myz47m',
  ),
    171 => 
  array (
    'name' => 'MRE',
    'host' => 'localhost',
    'dbname' => 'cloudso1_mre',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_mre',
    'dbpassword' => 'myz47m',
  ),
    172 => 
  array (
    'name' => 'VETZ2',
    'host' => 'localhost',
    'dbname' => 'cloudso1_vetz2',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_vetz2',
    'dbpassword' => 'myz47m',
  ),
    173 => 
  array (
    'name' => 'UNIFIED',
    'host' => 'localhost',
    'dbname' => 'cloudso1_unified',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_unified',
    'dbpassword' => 'myz47m',
  ),
 174 => 
  array (
    'name' => 'SPECTRA',
    'host' => 'localhost',
    'dbname' => 'cloudso1_spectra2',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_spectra2',
    'dbpassword' => 'myz47m',
  ), 
   175 => 
  array (
    'name' => 'BALOCH1',
    'host' => 'localhost',
    'dbname' => 'cloudso1_baloch1',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_baloch1',
    'dbpassword' => 'myz47m',
  ),
     176 => 
  array (
    'name' => 'MEI',
    'host' => 'localhost',
    'dbname' => 'cloudso1_mei2',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_mei2',
    'dbpassword' => 'myz47m',
  ),
     177 => 
  array (
    'name' => 'CW2',
    'host' => 'localhost',
    'dbname' => 'cloudso1_cw2',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_cw2',
    'dbpassword' => 'myz47m',
  ),
    178 => 
  array (
    'name' => 'LUBRIZONEPAKISTAN',
    'host' => 'localhost',
    'dbname' => 'cloudso1_lubrizonepakistan',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_lubrizonepakistan',
    'dbpassword' => 'myz47m',
  ),
   179 => 
  array (
    'name' => 'SACAPL',
    'host' => 'localhost',
    'dbname' => 'cloudso1_sacapl',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_sacapl',
    'dbpassword' => 'myz47m',
  ),
  180 => 
  array (
    'name' => 'TRISTAR',
    'host' => 'localhost',
    'dbname' => 'cloudso1_tristar',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_tristar',
    'dbpassword' => 'myz47m',
  ),
  181 => 
  array (
    'name' => 'AAT2',
    'host' => 'localhost',
    'dbname' => 'cloudso1_aat2',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_aat2',
    'dbpassword' => 'myz47m',
  ),
  182 => 
  array (
    'name' => 'UPVC',
    'host' => 'localhost',
    'dbname' => 'cloudso1_upvc',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_upvc',
    'dbpassword' => 'myz47m',
  ),
  183 => 
  array (
    'name' => 'SERVICES',
    'host' => 'localhost',
    'dbname' => 'cloudso1_services',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_services',
    'dbpassword' => 'myz47m',
  ),
   184 => 
  array (
    'name' => 'KAUSARMASALA',
    'host' => 'localhost',
    'dbname' => 'cloudso1_kausarmasala',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_kausarmasala',
    'dbpassword' => 'myz47m',
  ),
     185 => 
  array (
    'name' => 'SALMANENT',
    'host' => 'localhost',
    'dbname' => 'cloudso1_salmanent',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_salmanent',
    'dbpassword' => 'myz47m',
  ),
    186 => 
  array (
    'name' => 'TELEC',
    'host' => 'localhost',
    'dbname' => 'cloudso1_telec',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_telec',
    'dbpassword' => 'myz47m',
  ),
  187 => 
  array (
    'name' => 'CASAMIATAX',
    'host' => 'localhost',
    'dbname' => 'cloudso1_casamiatax',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_casamiatax',
    'dbpassword' => 'myz47m',
  ),
    188 => 
  array (
    'name' => 'APS',
    'host' => 'localhost',
    'dbname' => 'cloudso1_aps',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_aps',
    'dbpassword' => 'myz47m',
  ),
   189 => 
  array (
    'name' => 'SACASHOP',
    'host' => 'localhost',
    'dbname' => 'cloudso1_sacashop',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_sacashop',
    'dbpassword' => 'myz47m',
  ),
     190 => 
  array (
    'name' => 'HAMMER',
    'host' => 'localhost',
    'dbname' => 'cloudso1_hammer',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_hammer',
    'dbpassword' => 'myz47m',
  ),
    191 => 
  array (
    'name' => 'SACAKHI',
    'host' => 'localhost',
    'dbname' => 'cloudso1_sacakhi',
    'collation' => 'utf8_xx',
    'tbpref' => '0_',
    'dbuser' => 'cloudso1_sacakhi',
    'dbpassword' => 'myz47m',
  ),
);