<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Waterm extends CI_Model{
    
    //You Know lah ini login
    function login($f1,$f2){
      $this->db->select('*');
      $this->db->from('user');
      $this->db->where($f1);
      $this->db->where($f2);
      $this->db->where('level', '1');
      $this->db->limit(1);
      $query = $this->db->get();
      if ($query->num_rows() == 0) {
          return FALSE;
      } else {
          return $query->result();
      }
    }

    //Get Detailed On another table
    function getail($f1,$f2){
      $this->db->from($f1);
      $this->db->where($f2);
      $query = $this->db->get();
      return $query->row();
    }

    //Checking Registered?
    function chkreg($f1, $f2){
      $this->db->select('*');
      $this->db->from($f1);
      $this->db->where($f2);
      $this->db->limit(1);
      $query = $this->db->get();
      if ($query->num_rows() == 0) {
          return TRUE;
      } else {
          return FALSE;
      }
    }

    //Add User
    function reg($f1){
      $this->db->insert('user', $f1);
      $query = $this->db->affected_rows();
      if ($query == 0) {
          return FALSE;
      }else{
          return $query;
      }
    }

    //Update di table user
    function uptabusr($f1,$f2){
      $this->db->where($f1);
      $this->db->update("user", $f2);
      $query = $this->db->affected_rows();
      if ($query == 0) {
          return FALSE;
      }else{
          return $query;
      }
    }

    //Fetching data Country
    function cofetch(){
        $this->db->from('country');
        $query = $this->db->get();
        if($query !== FALSE && $query->num_rows() > 0){
            $data = array();
            foreach ($query->result() as $row) {
                $data[] = array('country' => $row->Name);
            }
        }else{
            $data = FALSE;
        }
        return $data;
    }

    //Fetching data State/City
    function scfetch($f1, $f2){
        $this->db->select('*');
        $this->db->from('city');
        $this->db->where($f1);
        $this->db->order_by($f2);
        $query = $this->db->get();
        if($query !== FALSE && $query->num_rows() > 0){
            return $query->result();
        }else{
            return FALSE;
        }
    }

    function fetchfaf($f1){
        $this->db->from("kesukaan_pengguna");
        $this->db->where($f1);
        $query = $this->db->get();
        if($query !== FALSE && $query->num_rows() > 0){
            $data = array();
            foreach ($query->result() as $row) {
                $data[] = $row;
            }
        }else{
            $data = FALSE;
        }
        return $data;
    }

    //Ambil seluruh data
    function fetchdata($f1){
        if($f1=="jual"){
          $where = "sopic/";
        }elseif($f1=="voucer"){
          $where = "vopic/";
        }else{
          $where = "";
        }
        $this->db->from($f1);
        $query = $this->db->get();
        $data = array();
        if($query !== FALSE && $query->num_rows() > 0){
              foreach ($query->result_array() as $row) {
              $row['foto'] = base_url()."asset/".$where.$row['foto'];
              $data[] = $row;
            }
        }
        return $data;
    }

    //Ambil seluruh map
    function fetchmap($f1, $f2){
        $this->db->from($f1);
        if($f2 != FALSE){
            $x = 0;
            foreach($f2 as $f2){
                if($x < 1)
                $this->db->where("tipe", $f2->interest);
                else
                $this->db->or_where("tipe", $f2->interest);
                $x++;
            }
        }
        $query = $this->db->get();
        $data = array();
        $got = array();
        if($query !== FALSE && $query->num_rows() > 0){
              foreach ($query->result_array() as $row) {
              unset($got);
              $got [] = array('message' => 'Nothing to Show');
              $row['foto'] = base_url()."asset/plpic/".$row['foto'];
              $hit = $row['id'];
              $get = $this->db->from('review_tempat')->where('id_tempat', $hit)->join('user', 'user.surel = review_tempat.surel')->get();
              if($get->num_rows() > 0){
                unset($got);
                foreach($get->result() as $hit){
                    $got[] = array(
                        'id_review' => $hit->id,
                        'nama' => $hit->namadepan." ".$hit->namabelakang,
                        'star' => $hit->bintang,
                        'komentar' => $hit->komentar,
                    );
                }
              }
              $x = array('review' => $got);
              $data[] = array_merge($row,$x);
            }
        }
        return $data;
    }

    //Ambil seluruh event
    function fetchev($f1){
        $this->db->from($f1);
        $query = $this->db->get();
        $data = array();
        if($query !== FALSE && $query->num_rows() > 0){
            foreach ($query->result_array() as $row) {
              $hit = $row['id'];
              $$row['foto'] = base_url()."asset/evpic/".$row['foto'];
              $get0 = $this->db->from('suka_event')->where('id_event', $hit)->get()->num_rows();
              $x = array('like' => $get0);
              $get1 = $this->db->from('share_event')->where('id_event', $hit)->get()->num_rows();
              $y = array('share' => $get1);
              $data[] = array_merge($row,$x,$y);
            }
        }
        return $data;
    }

    //Check Event ada/tidak
    function chkev($f1){
      $this->db->select('*');
      $this->db->from('event');
      $this->db->where($f1);
      $this->db->limit(1);
      $query = $this->db->get();
      if ($query->num_rows() == 0) {
          return FALSE;
      } else {
          return TRUE;
      }
    }

    //Check share/like/other yet?
    function chsm($f1,$f2,$f3){
      $this->db->select('*');
      $this->db->from($f1);
      $this->db->where($f2);
      $this->db->where($f3);
      $this->db->limit(1);
      $query = $this->db->get();
      if ($query->num_rows() == 0) {
          return FALSE;
      } else {
          return $query->result();
      }
    }

    //Add share/like/other
    function addsm($f1, $f2){
      $this->db->insert($f1, $f2);
      $query = $this->db->affected_rows();
      if ($query == 0) {
          return FALSE;
      }else{
          return $query;
      }
    }

    //Update data
    function uprev($f1, $f2, $f3, $f4){
        $this->db->where($f2);
        $this->db->where($f3);
        $this->db->update($f1,$f4);
        $query = $this->db->affected_rows();
        if ($query == 0) {
            return FALSE;
        }else{
            return $query;
        }
      }

    //Delete like
    function delsm($f1, $f2){
      $this->db->where($f2);
      $this->db->delete($f1);
      $query = $this->db->affected_rows();
      if ($query == 0) {
          return FALSE;
      }else{
          return $query;
      }
    }

    //Point updating
    function pntud($f1, $f2){
      $this->db->set("poin", "poin$f1", false);
      $this->db->where($f2);
      $this->db->update("user");
      $query = $this->db->affected_rows();
      if ($query == 0) {
          return FALSE;
      }else{
          return $query;
      }
    }

    //Save Monitor monthly
    function saved_mon($f1, $f2){
        $this->db->select('*');
        $this->db->from('konsumsi');
        $this->db->where($f1);
        $this->db->where("DATE_FORMAT(tgl,'%Y-%m')", $f2);
        $query = $this->db->get();
        if ($query->num_rows() == 0) {
            return FALSE;
        } else {
            return $query->result_array();
        }
    }

    //Save Monitor Daily
    function saved_day($f1, $f2){
        $this->db->select('*');
        $this->db->from('konsumsi');
        $this->db->where($f1);
        $this->db->where($f2);
        $this->db->limit(1);
        $query = $this->db->get();
        if ($query->num_rows() == 0) {
            return FALSE;
        } else {
            return $query->result_array();
        }
    }

    //Fetching data Interest
    function infetch($f1, $f2 = FALSE){
        $this->db->from($f1);
        if($f2 != FALSE){
            $this->db->where($f2);
        }
        $query = $this->db->get();
        if($query !== FALSE && $query->num_rows() > 0){
            $data = array();
            foreach ($query->result() as $row) {
                $data[] = $row;
            }
        }else{
            $data = FALSE;
        }
        return $data;
    }

    //Add Interest
    function addinter($f1){
        $this->db->insert('kesukaan_pengguna', $f1);
        $query = $this->db->affected_rows();
        if ($query == 0) {
            return FALSE;
        }else{
            return $query;
        }
    }

    //Delete Interest
    function delinter($f1){
        $this->db->where($f1);
        $this->db->delete('kesukaan_pengguna');
        $query = $this->db->affected_rows();
        if ($query == 0) {
            return FALSE;
        }else{
            return $query;
        }
    }

}
//End Of File -> Made By: DzEN/DzEN <-