<?php

/* Copyright (C) 2001-2002	Rodolphe Quiedeville	<rodolphe@quiedeville.org>
 * Copyright (C) 2003		Jean-Louis Bergamo		<jlb@j1b.org>
 * Copyright (C) 2004-2012	Laurent Destailleur		<eldy@users.sourceforge.net>
 * Copyright (C) 2012		Regis Houssin			<regis.houssin@capnetworks.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 *      \file       htdocs/societe/admin/contact_extrafields.php
 * 		\ingroup    societe
 * 		\brief      Page to setup extra fields of contact
 */
require '../../main.inc.php';
require_once DOL_DOCUMENT_ROOT . '/core/lib/company.lib.php';
require_once DOL_DOCUMENT_ROOT . '/core/class/extrafields.class.php';

$langs->load("companies");
$langs->load("admin");

$extrafields = new ExtraFields($db);
$form = new Form($db);

// List of supported format
$tmptype2label = ExtraFields::$type2label;
$type2label = array('');
foreach ($tmptype2label as $key => $val)
    $type2label[$key] = $langs->trans($val);

$action = GETPOST('action', 'alpha');
$attrname = GETPOST('attrname', 'alpha');
$elementtype = 'socpeople'; //Must be the $element of the class that manage extrafield

if (!$user->admin)
    accessforbidden();


/*
 * Actions
 */

require DOL_DOCUMENT_ROOT . '/core/actions_extrafields.inc.php';



/*
 * View
 */

$textobject = $langs->transnoentitiesnoconv("ContactsAddresses");

$help_url = 'EN:Module Third Parties setup|FR:Paramétrage_du_module_Tiers';
llxHeader('', $langs->trans("CompanySetup"), $help_url);


$linkback = '<a href="' . DOL_URL_ROOT . '/admin/modules.php">' . $langs->trans("BackToModuleList") . '</a>';
print_fiche_titre($langs->trans("CompanySetup"), $linkback, 'title_setup');


$head = societe_admin_prepare_head();

dol_fiche_head($head, 'attributes_contacts', $langs->trans("ThirdParties"), 0, 'company');

require DOL_DOCUMENT_ROOT . '/core/tpl/admin_extrafields_view.tpl.php';

dol_fiche_end();


// Buttons
if ($action != 'create' && $action != 'edit') {
    print '<div class="tabsAction">';
    print "<a class=\"butAction\" href=\"" . $_SERVER["PHP_SELF"] . "?action=create\">" . $langs->trans("NewAttribute") . "</a>";
    print "</div>";
}


/* * ************************************************************************* */
/*                                                                            */
/* Creation d'un champ optionnel
  /* */
/* * ************************************************************************* */

if ($action == 'create') {
    print "<br>";
    print_titre($langs->trans('NewAttribute'));

    require DOL_DOCUMENT_ROOT . '/core/tpl/admin_extrafields_add.tpl.php';
}

/* * ************************************************************************* */
/*                                                                            */
/* Edition d'un champ optionnel                                               */
/*                                                                            */
/* * ************************************************************************* */
if ($action == 'edit' && !empty($attrname)) {
    print "<br>";
    print_titre($langs->trans("FieldEdition", $attrname));

    require DOL_DOCUMENT_ROOT . '/core/tpl/admin_extrafields_edit.tpl.php';
}

llxFooter();

$db->close();
