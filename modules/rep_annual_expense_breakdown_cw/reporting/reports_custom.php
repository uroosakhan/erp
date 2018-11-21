<?php

global $reports;

$reports->addReport(RC_GL,"_annual_expense_breakdown_cw",_('Profit & Loss - Detailed'),
       array(  _('Report Period') => 'DATEENDM',
                       _('Dimension') => 'DIMENSIONS1',
                       _('Comments') => 'TEXTBOX',
                       _('Destination') => 'DESTINATION'));
?>
