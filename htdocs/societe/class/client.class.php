<?php

/* Copyright (C) 2004      Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2005-2012 Regis Houssin        <regis.houssin@capnetworks.com>
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
 *   	\file       htdocs/societe/class/client.class.php
 * 		\ingroup    societe
 * 		\brief      File for class of customers
 */
include_once DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php';

/**
 * 	Class to manage customers or prospects
 */
class Client extends Societe {

    var $db;
    var $next_prev_filter = "te.client in (1,2,3)"; // Used to add a filter in Form::showrefnav method
    var $cacheprospectstatus = array();

    /**
     *  Constructor
     *
     *  @param	DoliDB	$db		Database handler
     */
    function __construct($db, $dbNanapet=null) {
        $this->db = $db;
        $this->dbNanapet = $dbNanapet;
    }

    /**
     *  Load indicators into this->nb for board
     *
     *  @return     int         <0 if KO, >0 if OK
     */
    function load_state_board() {
        global $user;

        $this->nb = array("customers" => 0, "prospects" => 0);
        $clause = "WHERE";

        $sql = "SELECT count(s.rowid) as nb, s.client";
        $sql.= " FROM " . MAIN_DB_PREFIX . "societe as s";
        if (!$user->rights->societe->client->voir && !$user->societe_id) {
            $sql.= " LEFT JOIN " . MAIN_DB_PREFIX . "societe_commerciaux as sc ON s.rowid = sc.fk_soc";
            $sql.= " WHERE sc.fk_user = " . $user->id;
            $clause = "AND";
        }
        $sql.= " " . $clause . " s.client IN (1,2,3)";
        $sql.= ' AND s.entity IN (' . getEntity($this->element, 1) . ')';
        $sql.= " GROUP BY s.client";

        $resql = $this->db->query($sql);
        if ($resql) {
            while ($obj = $this->db->fetch_object($resql)) {
                if ($obj->client == 1 || $obj->client == 3)
                    $this->nb["customers"]+=$obj->nb;
                if ($obj->client == 2 || $obj->client == 3)
                    $this->nb["prospects"]+=$obj->nb;
            }
            $this->db->free($resql);
            return 1;
        }
        else {
            dol_print_error($this->db);
            $this->error = $this->db->lasterror();
            return -1;
        }
    }

    /**
     *  Load array of prospect status
     *
     *  @param	int		$active     1=Active only, 0=Not active only, -1=All
     *  @return int					<0 if KO, >0 if OK
     */
    function loadCacheOfProspStatus($active = 1) {
        global $langs;

        $sql = "SELECT id, code, libelle as label FROM " . MAIN_DB_PREFIX . "c_stcomm";
        if ($active >= 0)
            $sql.=" WHERE active = " . $active;
        $resql = $this->db->query($sql);
        $num = $this->db->num_rows($resql);
        $i = 0;
        while ($i < $num) {
            $obj = $this->db->fetch_object($resql);
            $this->cacheprospectstatus[$obj->id] = array('id' => $obj->id, 'code' => $obj->code, 'label' => ($langs->trans("ST_" . strtoupper($obj->code)) == "ST_" . strtoupper($obj->code)) ? $obj->label : $langs->trans("ST_" . strtoupper($obj->code)));
            $i++;
        }
        return 1;
    }
    
    public function getDiscountOfCustomer($user){
        if($this->dbNanapet != null && $this->db != null){
            $group_member = "";
            $sql = "SELECT GroupMember as group_member"
                . " FROM user"
                . " WHERE email='".$user."'";
            $result = $this->dbNanapet->query($sql);
            if($result) {
                if ($this->dbNanapet->num_rows($sql) > 0) {
                    $obj = $this->dbNanapet->fetch_object($sql);
                    $group_member = $obj->group_member;
                    if($group_member != 'Chưa là thành viên' && !empty($group_member)) {
                        $sql = "SELECT DisCount as discount"
                            . " FROM GroupMember"
                            . " WHERE NameGroupMember='".$group_member."'";
                        $result = $this->dbNanapet->query($sql);
                        if($result) {
                            if ($this->dbNanapet->num_rows($sql) > 0) {
                                $obj = $this->dbNanapet->fetch_object($sql);
                                $result_discount = array($obj->discount,$group_member);
                                return $result_discount;
                            } else {
                                return 0;
                            }
                        }                                                
                    } else {
                        $sql = "SELECT honors as honors"
                            . " FROM scores"
                            . " WHERE user='".$user."'";
                        $result = $this->dbNanapet->query($sql);
                        if($result) {
                            if ($this->dbNanapet->num_rows($sql) > 0) {
                                $obj = $this->dbNanapet->fetch_object($sql);
                                $honors = $obj->honors;
                                if($honors != 'normal' && $honors != 'Normal'){
                                    $sql = "SELECT DisCount as discount"
                                        . " FROM VIPCustomer"
                                        . " WHERE NameVIPCustomer='".$honors."'";
                                    $result = $this->dbNanapet->query($sql);
                                    if($result) {
                                        if ($this->dbNanapet->num_rows($sql) > 0) {
                                            $obj = $this->dbNanapet->fetch_object($sql);                                            
                                            $result_discount = array($obj->discount,$honors);
                                            return $result_discount;
                                        }
                                    }
                                } else {
                                    return 0;
                                }
                            }
                        }
                    }                                        
                } else {
                    return 0;
                }
            }
        }               
        return 0;        
    }        
    
    // NanaPet
    public function addScoreForCustomer($user, $total){
        if($this->dbNanapet != null && $this->db != null){
            $sql = "SELECT GroupMember FROM user WHERE email ='".$user."'";	
            $result = $this->dbNanapet->query($sql);
            if($result) {
                if ($this->dbNanapet->num_rows($sql) > 0) {
                    $obj = $this->dbNanapet->fetch_object($sql);
                    $GroupMember = $obj->GroupMember;
                    if ($GroupMember == 'Chưa là thành viên') {
                        $sql = "SELECT PriceScore FROM PriceOfUnit";
                        $result = $this->dbNanapet->query($sql);
                        if($result) {
                            if ($this->dbNanapet->num_rows($sql) > 0) {
                                $obj = $this->dbNanapet->fetch_object($sql);
                                $PriceOfUnit = $obj->PriceScore;
                                $bill_score = floor(intval($total / $PriceOfUnit));
                                $old_score = $this->checkExistScore($user);
                                if ($old_score >= 0) {
                                    $new_score =  $old_score + $bill_score;
                                    if ($this->updateScore($user, $new_score) > 0) {
                                        return 1;
                                    }
                                } else {
                                    if ($this->insertScore($user, $bill_score) > 0) {
                                        return 1;
                                    }                                            
                                }
                            }
                        }
                    } else {
                        // Not update score in case Group Member != Chua la thanh vien
                        return 1;
                    }
                }
            }
        }
        return 0;
    }
    
    public function checkExistScore($user) {
        if($this->dbNanapet != null){
            $sql = "SELECT score, user "
                . " FROM scores WHERE user ='".$user."'";	
            $result = $this->dbNanapet->query($sql);
            if($result) {
                if ($this->dbNanapet->num_rows($sql) > 0) {
                    $obj = $this->dbNanapet->fetch_object($sql);
                    if (!empty($obj->user)) {
                        return $obj->score;
                    } 
                }
            }             
        }
        return -1;
    }    
    
    public function updateScore($user, $new_score) {
        if($this->dbNanapet != null){
            $this->dbNanapet->begin();
            $sql = "UPDATE scores"
                . " SET score = $new_score WHERE user ='".$user."'";
            $result = $this->dbNanapet->query($sql);
            if($result) {
                $this->dbNanapet->commit();
                return 1;
            } else {
                $this->dbNanapet->rollback();
            }
        }
        return -1;
    }
    
    public function insertScore($user, $new_score) {
        if($this->dbNanapet != null){            
            $this->dbNanapet->begin();
            $sql = "INSERT INTO scores "
                    . "(user, score, honors, fee4Ever, scorelevel, scoreaward, scorebirthday)"
                . " VALUE ('".$user. "',".$new_score. ",'Normal',0,0,0,0)";
            $result = $this->dbNanapet->query($sql);
            if($result) {
                $this->dbNanapet->commit();
                return 1;
            } else {
                $this->dbNanapet->rollback();
            }
        }        
        return -1;
    }    
}