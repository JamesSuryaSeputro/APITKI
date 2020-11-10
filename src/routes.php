<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

return function (App $app) {
    $container = $app->getContainer();

    $app->get('a/[{name}]', function (Request $request, Response $response, array $args) use ($container) {
        // Sample log message
        $container->get('logger')->info("Slim-Skeleton '/' route");

        // Render index view
        return $container->get('renderer')->render($response, 'index.phtml', $args);
    });
    
    $app->post('/loginuser', function ($request, $response, $args) {
        $username = $request -> getParam('username');
        $password = $request -> getParam('password');

        $sth = $this->db->prepare("SELECT a.id_user as iduser, a.username as username, a.nama AS nama, a.year, b.status AS status_document, c.status AS status_pembayaran FROM tbl_user AS a INNER JOIN tabel_doc_user AS b ON a.id_user = b.iduser INNER JOIN tabel_pembayaran AS c ON a.id_user = c.id_user WHERE a.username = :username AND a.password = :password");
        $sth ->bindParam(':username',$username);
        $sth ->bindParam(':password',$password);
        $sth->execute();
        $datas = $sth->fetchAll();
        return $this->response->withJson($datas[0]);
    });

    $app->post('/check_passed_user_greater_than', function ($request, $response, $args) {
        $iduser = $request -> getParam('iduser');

        $sth = $this->db->prepare("SELECT EXISTS(SELECT * FROM `tabel_score_average` WHERE avg_score > 75 AND id_user = :iduser) AS status");
        $sth ->bindParam(':iduser',$iduser);
        $sth->execute();
        $datas = $sth->fetchAll();
        return $this->response->withJson($datas[0]);
    });

    $app->post('/check_passed_user_lower_than', function ($request, $response, $args) {
        $iduser = $request -> getParam('iduser');

        $sth = $this->db->prepare("SELECT EXISTS(SELECT * FROM `tabel_score_average` WHERE avg_score < 75 AND id_user = :iduser) AS status");
        $sth ->bindParam(':iduser',$iduser);
        $sth->execute();
        $datas = $sth->fetchAll();
        return $this->response->withJson($datas[0]);
    });
    
    $app->post('/update_status_tki', function ($request, $response, $args) {
        $iduser = $request -> getParam('iduser');

        $sth = $this->db->prepare("UPDATE `tbl_user` SET `status` = 1 WHERE id_user = :iduser");
        $sth ->bindParam(':iduser',$iduser);
        if($sth->execute()){
            return $response->withJson(["status" => "1"], 200);
        }    else{
            return $response->withJson(["status" => "0"], 400);
        }
    });

    $app->post('/update_status_calontki', function ($request, $response, $args) {
        $iduser = $request -> getParam('iduser');

        $sth = $this->db->prepare("UPDATE `tbl_user` SET `status` = 0 WHERE id_user = :iduser");
        $sth ->bindParam(':iduser',$iduser);
        if($sth->execute()){
            return $response->withJson(["status" => "1"], 200);
        }    else{
            return $response->withJson(["status" => "0"], 400);
        }
    });

    $app->post('/loginpegawai', function ($request, $response, $args) {
        $username = $request -> getParam('username');
        $password = $request -> getParam('password');

        $sth = $this->db->prepare("SELECT a.id_pegawai as iduser, a.username as username, a.nama_pegawai AS nama, a.year FROM tabel_pegawai AS a WHERE a.username = :username AND a.password = :password");
        $sth ->bindParam(':username',$username);
        $sth ->bindParam(':password',$password);
        $sth->execute();
        $datas = $sth->fetchAll();
        return $this->response->withJson($datas[0]);
    });

    $app->post('/loginpelatih', function ($request, $response, $args) {
        $username = $request -> getParam('username');
        $password = $request -> getParam('password');

        $sth = $this->db->prepare("SELECT a.id_pelatih as iduser, a.username as username, a.nama_pelatih AS nama, a.year FROM tabel_pelatih AS a WHERE a.username = :username AND a.password = :password");
        $sth ->bindParam(':username',$username);
        $sth ->bindParam(':password',$password);
        $sth->execute();
        $datas = $sth->fetchAll();
        return $this->response->withJson($datas[0]);
    });
    
    $app->post('/pembayaranuser', function ($request, $response, $args) {
        $iduser= $request -> getParam('iduser');        

        $sth = $this->db->prepare("SELECT a.id_pembayaran as idpembayaran,bukti_tf as img_bukti,a.status as status,a.datecreated as tanggal,b.nama as nama FROM `tabel_pembayaran` as a  RIGHT JOIN tbl_user as b on a.id_user = b.id_user Where b.id_user = :iduser");
        $sth ->bindParam(':iduser',$iduser);
        $sth->execute();
        $datas = $sth->fetchAll();
        return $this->response->withJson($datas[0]);
    });

    $app->post('/checkdocuser', function ($request, $response, $args) {
        $iduser= $request -> getParam('iduser');        

        $sth = $this->db->prepare("SELECT a.status as status,IFNULL(a.scan_ktp,'') as scanktp,IFNULL(a.scan_kompensasi,'')as scankompensasi,IFNULL(a.scan_surat_kesehatan,'') as scansuratkesehatan,IFNULL(a.scan_surat_kerja,'') as scansuratkerja FROM tabel_doc_user as a WHERE a.iduser = :iduser");
        $sth ->bindParam(':iduser',$iduser);
        $sth->execute();
        $datas = $sth->fetchAll();
        return $this->response->withJson($datas[0]);
    });

    $app->post('/uploadpembayaran', function ($request, $response, $args) {
        $iduser= $request -> getParam('iduser');        
        $buktipembayaran = $request-> getParam('buktipembayaran');
        $sth = $this->db->prepare("UPDATE `tabel_pembayaran` SET `bukti_tf`=:buktipembayaran WHERE id_user = :iduser");
        $sth ->bindParam(':iduser',$iduser);
        $sth ->bindParam(':buktipembayaran',$buktipembayaran);

        if($sth->execute()){
            return $response->withJson(["status" => 1], 200);
        }    else{
            return $response->withJson(["status" => 0], 400);
        }
    });

    $app->post('/uploaddocuser', function ($request, $response, $args) {
        $iduser= $request -> getParam('iduser');        
        $scanktp = $request-> getParam('scanktp');
        $scankompensasi = $request -> getParam('scankompensasi');
        $scansuratkesehatan = $request -> getParam('scansuratkesehatan');
        $scansuratkerja =$request->getParam('scansuratkerja');
        $sth = $this->db->prepare("UPDATE `tabel_doc_user` SET `scan_ktp`=COALESCE(:scanktp,scan_ktp),`scan_kompensasi`=COALESCE(:scankompensasi,scan_kompensasi),`scan_surat_kesehatan`=COALESCE(:suratkesehatan,scan_surat_kesehatan),`scan_surat_kerja`=COALESCE(:suratkerja,scan_surat_kesehatan) WHERE iduser = :iduser");
        $sth ->bindParam(':iduser',$iduser);
        $sth ->bindParam(':scanktp',$scanktp);
        $sth ->bindParam(':scankompensasi',$scankompensasi);
        $sth ->bindParam(':suratkesehatan',$scansuratkesehatan);
        $sth ->bindParam(':suratkerja',$scansuratkerja);

        if($sth->execute()){
            return $response->withJson(["status" => 1], 200);
        }    else{
            return $response->withJson(["status" => 0], 400);
        }
    });

    $app->post('/addjadwal', function ($request, $response, $args) {
        $iduser = $request -> getParam('iduser');        
        $idsubject = $request-> getParam('idsubject');
        $tglmulai = $request-> getParam('tglmulai');
        $tglselesai = $request-> getParam('tglselesai');
    
        $sth = $this->db->prepare("INSERT INTO `tabel_jadwal_pelatihan`(`id_subject`, `id_pelatih`, `tgl_mulai`, `tgl_selesai`) VALUES (:idsubject,:iduser,:tglmulai,:tglselesai)");
        $sth ->bindParam(':iduser',$iduser);
        $sth ->bindParam(':idsubject',$idsubject);
        $sth ->bindParam(':tglmulai',$tglmulai);
        $sth ->bindParam(':tglselesai',$tglselesai);

        if($sth->execute()){
            return $response->withJson(["status" => 1], 200);
        }    else{
            return $response->withJson(["status" => 0], 400);
        }
    });

    $app->post('/adddetailjadwal', function ($request, $response, $args) {      
        $idjadwal = $request-> getParam('idjadwal');
        $hari = $request-> getParam('hari');
        $tanggal = $request-> getParam('tanggal');
        $jammulai = $request-> getParam('jammulai');
        $jamselesai = $request-> getParam('jamselesai');
    
        $sth = $this->db->prepare("INSERT INTO `tabel_jadwal_pelatihan_detail`(`id_jadwal`, `hari`, `tanggal`, `jam_mulai`, `jam_selesai`) VALUES (:idjadwal, :hari, :tanggal, :jammulai, :jamselesai)");
        $sth ->bindParam(':idjadwal',$idjadwal);
        $sth ->bindParam(':hari',$hari);
        $sth ->bindParam(':tanggal',$tanggal);
        $sth ->bindParam(':jammulai',$jammulai);
        $sth ->bindParam(':jamselesai',$jamselesai);

        if($sth->execute()){
            return $response->withJson(["status" => 1], 200);
        }    else{
            return $response->withJson(["status" => 0], 400);
        }
    });

    $app->post('/get_jadwal_pelatihan_all', function ($request, $response, $args) {
        $iduser = $request -> getParam('iduser');

        $sth = $this->db->prepare("SELECT a.`id_jadwal`, a.`id_subject`,b.nama_subject, a.`tgl_mulai`, a.`tgl_selesai` FROM `tabel_jadwal_pelatihan` as a INNER JOIN tabel_subject_pelatihan as b on b.id_subject = a.id_subject WHERE a.status = 1 and a.id_pelatih = :iduser");
        $sth ->bindParam(':iduser',$iduser);
        $sth->execute();
        $datas = $sth->fetchAll();
        return $this->response->withJson($datas);
    });

    $app->post('/get_detail_jadwal_pelatihan', function ($request, $response, $args) {
        $idjadwal = $request -> getParam('idjadwal');

        $sth = $this->db->prepare("SELECT * FROM `tabel_jadwal_pelatihan_detail` WHERE id_jadwal = :idjadwal");
        $sth ->bindParam(':idjadwal',$idjadwal);
        $sth->execute();
        $datas = $sth->fetchAll();
        return $this->response->withJson($datas);
    });

    $app->post('/get_detail_jadwal_pelatihan_user', function ($request, $response, $args) {
        $idjadwal = $request -> getParam('idjadwal');
        $iduser = $request -> getParam('iduser');

        $sth = $this->db->prepare("SELECT DISTINCT b.hari, b.tanggal, b.jam_mulai, b.jam_selesai, IFNULL(c.status_presensi,0) AS status_presensi, IFNULL(d.nilai, 'materi') AS nilai FROM tabel_pelatihan_user as a INNER JOIN tabel_jadwal_pelatihan_detail as b on a.id_jadwal = b.id_jadwal LEFT JOIN tabel_presensi as c on c.id_jadwal_detail = b.id_jadwal_detail LEFT JOIN tabel_nilai as d ON b.id_jadwal_detail = d.id_jadwal_detail WHERE a.id_jadwal = :idjadwal AND a.id_user = :iduser");
        $sth ->bindParam(':idjadwal',$idjadwal);
        $sth ->bindParam(':iduser',$iduser);
        $sth->execute();
        $datas = $sth->fetchAll();
        return $this->response->withJson($datas);
    });
    
    // $app->post('/get_detail_jadwal_pelatihan_user', function ($request, $response, $args) {
    //     $idjadwal = $request -> getParam('idjadwal');
    //     $iduser = $request -> getParam('iduser');

    //     $sth = $this->db->prepare("SELECT d.hari, d.tanggal, d.jam_mulai, d.jam_selesai, IFNULL(e.status_presensi,0) AS status_presensi, IFNULL(f.nilai,0) AS nilai FROM tabel_pelatihan_user as a INNER JOIN tbl_user as b on a.id_user = b.id_user RIGHT JOIN tabel_jadwal_pelatihan as c on a.id_jadwal = c.id_jadwal INNER JOIN tabel_jadwal_pelatihan_detail as d on c.id_jadwal = d.id_jadwal LEFT JOIN tabel_presensi as e on e.id_jadwal_detail = d.id_jadwal_detail LEFT JOIN tabel_nilai as f ON d.id_jadwal_detail = f.id_jadwal_detail WHERE a.id_jadwal = :idjadwal AND a.id_user = :iduser");
    //     $sth ->bindParam(':idjadwal',$idjadwal);
    //     $sth ->bindParam(':iduser',$iduser);
    //     $sth->execute();
    //     $datas = $sth->fetchAll();
    //     return $this->response->withJson($datas);
    // });

    $app->post('/get_jadwal_pelatihan_user', function ($request, $response, $args) {
        $iduser = $request -> getParam('iduser');
        $sth = $this->db->prepare("SELECT DISTINCT b.`id_jadwal`, b.`id_subject`, c.nama_subject, b.`tgl_mulai`, b.`tgl_selesai`, d.`nama_pelatih`, a.`id_user`, IFNULL(e.`avg_score`, 0) AS avg_score FROM `tabel_pelatihan_user` AS a INNER JOIN `tabel_jadwal_pelatihan` as b ON a.id_jadwal = b.id_jadwal INNER JOIN `tabel_subject_pelatihan` AS c ON b.id_subject = c.id_subject INNER JOIN `tabel_pelatih` AS d ON b.id_pelatih = d.id_pelatih LEFT JOIN tabel_score_average AS e ON a.id_jadwal = e.id_jadwal WHERE a.status = 1 AND a.id_user = :iduser");
        $sth ->bindParam(':iduser',$iduser);
        $sth->execute();
        $datas = $sth->fetchAll();
        return $this->response->withJson($datas);
    });

    // $app->post('/get_detail_jadwal_pelatihan_user', function ($request, $response, $args) {
    //     $idjadwal = $request -> getParam('idjadwal');
    //     $iduser = $request -> getParam('iduser');
    //     $idjadwaldetail = $request -> getParam('idjadwaldetail');

    //     $sth = $this->db->prepare("SELECT d.hari, d.tanggal, d.jam_mulai, d.jam_selesai, IFNULL(e.status_presensi,0) AS status_presensi, IFNULL(f.nilai,0) AS nilai FROM tabel_pelatihan_user as a INNER JOIN tbl_user as b on a.id_user = b.id_user RIGHT JOIN tabel_jadwal_pelatihan as c on a.id_jadwal = c.id_jadwal INNER JOIN tabel_jadwal_pelatihan_detail as d on c.id_jadwal = d.id_jadwal LEFT JOIN tabel_presensi as e on e.id_jadwal_detail = d.id_jadwal_detail AND e.id_user = a.id_user LEFT JOIN tabel_nilai as f ON a.id_pelatihan = f.id_pelatihan AND e.id_user = a.id_user WHERE a.id_jadwal = :idjadwal AND a.id_user = :iduser AND d.id_jadwal_detail = :idjadwaldetail");
    //     $sth ->bindParam(':idjadwal',$idjadwal);
    //     $sth ->bindParam(':iduser',$iduser);
    //     $sth ->bindParam(':idjadwaldetail',$idjadwaldetail);
    //     $sth->execute();
    //     $datas = $sth->fetchAll();
    //     return $this->response->withJson($datas);
    // });
    
    // $app->post('/get_jadwal_pelatihan_user', function ($request, $response, $args) {
    //     $iduser = $request -> getParam('iduser');
    //     $sth = $this->db->prepare("SELECT b.`id_jadwal`, b.`id_subject`, c.nama_subject, b.`tgl_mulai`, b.`tgl_selesai`, d.`nama_pelatih`, a.`id_user` FROM `tabel_pelatihan_user` AS a INNER JOIN `tabel_jadwal_pelatihan` as b ON a.id_jadwal = b.id_jadwal INNER JOIN `tabel_subject_pelatihan` AS c ON b.id_subject = c.id_subject INNER JOIN `tabel_pelatih` AS d ON b.id_pelatih = d.id_pelatih WHERE a.status = 1 AND a.id_user = :iduser");
    //     $sth ->bindParam(':iduser',$iduser);
    //     $sth->execute();
    //     $datas = $sth->fetchAll();
    //     return $this->response->withJson($datas);
    // });

    $app->post('/delete_jadwal_pelatih', function ($request, $response, $args) {
        $idjadwal= $request -> getParam('idjadwal');

        $sth = $this->db->prepare("DELETE FROM `tabel_jadwal_pelatihan` WHERE id_jadwal = :idjadwal");
        $sth ->bindParam(':idjadwal',$idjadwal);

        if($sth->execute()){
            return $response->withJson(["status" => 1], 200);
        }    else{
            return $response->withJson(["status" => 0], 400);
        }
    });

    $app->post('/delete_detail_jadwal_pelatih', function ($request, $response, $args) {
        $idjadwaldetail = $request -> getParam('idjadwaldetail');

        $sth = $this->db->prepare("DELETE FROM `tabel_jadwal_pelatihan_detail` WHERE id_jadwal_detail = :idjadwaldetail");
        $sth ->bindParam(':idjadwaldetail',$idjadwaldetail);

        if($sth->execute()){
            return $response->withJson(["status" => 1], 200);
        }    else{
            return $response->withJson(["status" => 0], 400);
        }
    });

    $app->post('/get_user_all', function ($request, $response, $args) {
        $iddetailjadwal  = $request ->getParam('idjadwal');
        $sth = $this->db->prepare("SELECT c.id_user as id_user ,c.nama,c.jeniskelamin,c.date_created as tanggalterdaftar,c.passfoto FROM tbl_user as c where id_user NOT IN (SELECT a.id_user as id_user from tbl_user as a RIGHT JOIN tabel_pelatihan_user as b on b.id_user = a.id_user where b.id_jadwal = :idjadwal )");
        $sth ->bindParam('idjadwal',$idjadwal);
        $sth->execute();
        $datas = $sth->fetchAll();
        return $this->response->withJson($datas);
    });
    
        //     $sth = $this->db->prepare("SELECT a.id_pelatihan as id,b.nama as nama,b.date_created as tanggalterdaftar,b.jeniskelamin,b.passfoto FROM `tabel_pelatihan_user` as a INNER JOIN tbl_user as b on a.id_user = b.id_user where a.id_jadwal = :idjadwal AND a.status = 1"
    $app->post('/get_detail_user_pelatihan_presensi', function ($request, $response, $args) {
        $idjadwal = $request -> getParam('idjadwal');
        $idjadwaldetail = $request -> getParam('idjadwaldetail');
        $sth = $this->db->prepare("SELECT a.id_pelatihan as id, b.nama, b.date_created as tanggalterdaftar,b.jeniskelamin, b.passfoto, a.id_user,IFNULL(e.status_presensi,0) as status_presensi FROM tabel_pelatihan_user as a INNER JOIN tbl_user as b on a.id_user = b.id_user RIGHT JOIN tabel_jadwal_pelatihan as c on a.id_jadwal = c.id_jadwal INNER JOIN tabel_jadwal_pelatihan_detail as d on c.id_jadwal = d.id_jadwal LEFT JOIN tabel_presensi as e on e.id_jadwal_detail = d.id_jadwal_detail AND e.id_user = a.id_user WHERE a.id_jadwal = :idjadwal AND d.id_jadwal_detail = :idjadwaldetail");
        $sth ->bindParam(':idjadwal',$idjadwal);
        $sth ->bindParam(':idjadwaldetail',$idjadwaldetail);
        $sth->execute();
        $datas = $sth->fetchAll();
        return $this->response->withJson($datas);
    });

    $app->post('/get_detail_user_pelatihan_nilai', function ($request, $response, $args) {
        $idjadwal = $request -> getParam('idjadwal');
        $idjadwaldetail = $request -> getParam('idjadwaldetail');
        $sth = $this->db->prepare("SELECT a.id_pelatihan as id, b.nama, b.date_created as tanggalterdaftar,b.jeniskelamin, b.passfoto, a.id_user, IFNULL(e.nilai,0) as nilai FROM tabel_pelatihan_user as a INNER JOIN tbl_user as b on a.id_user = b.id_user RIGHT JOIN tabel_jadwal_pelatihan as c on a.id_jadwal = c.id_jadwal INNER JOIN tabel_jadwal_pelatihan_detail as d on c.id_jadwal = d.id_jadwal LEFT JOIN tabel_nilai as e on e.id_jadwal_detail = d.id_jadwal_detail AND a.id_pelatihan = e.id_pelatihan WHERE a.id_jadwal = :idjadwal AND d.id_jadwal_detail = :idjadwaldetail");
        $sth ->bindParam(':idjadwal',$idjadwal);
        $sth ->bindParam(':idjadwaldetail',$idjadwaldetail);
        $sth->execute();
        $datas = $sth->fetchAll();
        return $this->response->withJson($datas);
    });

    $app->post('/get_detail_user_pelatihan', function ($request, $response, $args) {
        $idjadwal = $request -> getParam('idjadwal');
        $sth = $this->db->prepare("SELECT a.id_pelatihan as id, b.nama as nama, b.date_created as tanggalterdaftar, b.jeniskelamin, b.passfoto, a.id_user FROM `tabel_pelatihan_user` as a INNER JOIN tbl_user as b on a.id_user = b.id_user where a.id_jadwal = :idjadwal AND a.status = 1");
        $sth ->bindParam(':idjadwal',$idjadwal);
        $sth->execute();
        $datas = $sth->fetchAll();
        return $this->response->withJson($datas);
    });

    $app->post('/add_user_pelatihan', function ($request, $response, $args) {
        $idjadwal = $request -> getParam('idjadwal');
        $iduser = $request -> getParam('iduser');

        $sth = $this->db->prepare("INSERT INTO `tabel_pelatihan_user`(`id_jadwal`, `id_user`) VALUES (:idjadwal,:iduser)");
        $sth ->bindParam(':idjadwal',$idjadwal);
        $sth ->bindParam(':iduser',$iduser);
        if($sth->execute()){
            return $response->withJson(["status" => 1], 200);
        }    else{
            return $response->withJson(["status" => 0], 400);
        }
    });
    
    $app->post('/delete_user_jadwal', function ($request, $response, $args) {
        $iduser = $request -> getParam('idpelatihan');
        $sth = $this->db->prepare("DELETE FROM `tabel_pelatihan_user` WHERE id_pelatihan = :idpelatihan");
        $sth ->bindParam(':idpelatihan',$iduser);

        if($sth->execute()){
            return $response->withJson(["status" => 1], 200);
        }    else{
            return $response->withJson(["status" => 0], 400);
        }
    });

    $app->post('/get_detail_user_nilai', function ($request, $response, $args) {
        $idjadwal = $request -> getParam('idjadwal');

        $sth = $this->db->prepare("SELECT a.id_pelatihan as id, b.nama as nama, b.date_created as tanggalterdaftar, b.jeniskelamin, b.passfoto, IFNULL(c.nilai, 0) as nilai FROM `tabel_pelatihan_user` as a INNER JOIN tbl_user as b on a.id_user = b.id_user LEFT JOIN tabel_nilai as c on c.id_pelatihan = a.id_pelatihan where a.id_jadwal = :idjadwal AND a.status = 1");
        $sth ->bindParam(':idjadwal',$idjadwal);
        $sth->execute();
        $datas = $sth->fetchAll();
        return $this->response->withJson($datas);
    });

    // $app->post('/add_tabel_nilai', function ($request, $response, $args) {
    //     $iduser = $request -> getParam('idpelatihan');
    //     $nilai =  $request -> getParam('nilai');
    //     $sth = $this->db->prepare("call addnilai(:idpelatihan, :nilai)");
    //     $sth ->bindParam(':idpelatihan',$iduser);
    //     $sth ->bindParam(':nilai',$nilai);

    //     if($sth->execute()){
    //         return $response->withJson(["status" => 1], 200);
    //     }    else{
    //         return $response->withJson(["status" => 0], 400);
    //     }
    // });

    // $app->post('/add_presensi_ujian', function ($request, $response, $args) {
    //     $idpresensi = $request -> getParam('idpresensi');
    //     $idjadwaldetail = $request -> getParam('idjadwaldetail');
    //     $iduser = $request -> getParam('iduser');
    //     $statuspresensi = $request -> getParam('statuspresensi');

    //     $sth = $this->db->prepare("INSERT INTO `tabel_presensi`(`id_jadwal_detail`, `id_user`, `status_presensi`, `id_pelatihan`) VALUES (1, 1, 1, 2) ON DUPLICATE KEY UPDATE `id_jadwal_detail`=1,`id_user`=1,`status_presensi`=2,`id_pelatihan`=2");
    //     $sth ->bindParam(':idpresensi',$idpresensi);
    //     $sth ->bindParam(':idjadwaldetail',$idjadwaldetail);
    //     $sth ->bindParam(':iduser',$iduser);
    //     $sth ->bindParam(':statuspresensi',$statuspresensi);
    //     if($sth->execute()){
    //         return $response->withJson(["status" => 1, "idpresensi" => $idpresensi], 200);
    //     }    else{
    //         return $response->withJson(["status" => 0], 400);
    //     }
    // });

    // $app->post('/check_idpresensi', function ($request, $response, $args) {
    //     $email = $request->getParam('idpresensi');
    //     $sth = $this->db->prepare("SELECT ifnull(COUNT(*), 0) AS status, email FROM user WHERE email = :email");
    //     $sth->bindParam(":email", $email);
    //     $sth->execute();
    //     $todos = $sth->fetchAll();
    //     return $this->response->withJson($todos[0]);
    // });

    // $app->post('/add_presensi_ujian', function ($request, $response, $args) {
    //     $idpresensi = $request -> getParam('idpresensi');
    //     $idjadwaldetail = $request -> getParam('idjadwaldetail');
    //     $iduser = $request -> getParam('iduser');
    //     $statuspresensi = $request -> getParam('statuspresensi');

    //     $sth = $this->db->prepare("INSERT INTO `tabel_presensi` (`id_presensi`, `id_jadwal_detail`, `id_user`, `status_presensi`)
    //     SELECT :idpresensi, :idjadwaldetail, :iduser, :statuspresensi FROM DUAL WHERE NOT EXISTS 
    //     (SELECT `id_presensi` FROM `tabel_presensi` WHERE `id_presensi` = $idpresensi)");
    //     $sth ->bindParam(':idpresensi',$idpresensi);
    //     $sth ->bindParam(':idjadwaldetail',$idjadwaldetail);
    //     $sth ->bindParam(':iduser',$iduser);
    //     $sth ->bindParam(':statuspresensi',$statuspresensi);
    //     if($sth->execute()){
    //         return $response->withJson(["status" => 1, "idpresensi" => $idpresensi], 200);
    //     }    else{
    //         return $response->withJson(["status" => 0], 400);
    //     }
    // });

    //INSERT IGNORE INTO (`id_jadwal_detail`, `id_user`, `status_presensi`, `id_pelatihan`) SELECT `id_pelatihan` FROM `tabel_pelatihan_user`
    // $app->post('/add_presensi_ujian', function ($request, $response, $args) {
    //     $idjadwaldetail = $request -> getParam('idjadwaldetail');
    //     $iduser = $request -> getParam('iduser');
    //     $statuspresensi = $request -> getParam('statuspresensi');
    //     $idpelatihan = $request -> getParam('idpelatihan');

    //     $sth = $this->db->prepare("INSERT INTO `tabel_presensi`(`id_jadwal_detail`, `id_user`, `status_presensi`, `id_pelatihan`) VALUES (:idjadwaldetail, :iduser, :statuspresensi, :idpelatihan)
    //     ON DUPLICATE KEY UPDATE `id_jadwal_detail`=:idjadwaldetail,`id_user`=:iduser,`status_presensi`=:statuspresensi,`id_pelatihan`=:idpelatihan");
    //     $sth ->bindParam(':idjadwaldetail',$idjadwaldetail);
    //     $sth ->bindParam(':iduser',$iduser);
    //     $sth ->bindParam(':statuspresensi',$statuspresensi);
    //     $sth ->bindParam(':idpelatihan',$idpelatihan);
    //     if($sth->execute()){
    //         return $response->withJson(["status" => 1], 200);
    //     }    else{
    //         return $response->withJson(["status" => 0], 400);
    //     }
    // });

    $app->post('/add_nilai', function ($request, $response, $args) {
        $idpelatihan = $request -> getParam('idpelatihan');
        $idjadwaldetail = $request -> getParam('idjadwaldetail');
        $nilai = $request -> getParam('nilai');

        $sth = $this->db->prepare("INSERT INTO `tabel_nilai`(`id_pelatihan`, `id_jadwal_detail`, `nilai`) VALUES (:idpelatihan, :idjadwaldetail, :nilai)");
        $sth ->bindParam(':idpelatihan',$idpelatihan);
        $sth ->bindParam(':idjadwaldetail',$idjadwaldetail);
        $sth ->bindParam(':nilai',$nilai);
        if($sth->execute()){
            return $response->withJson(["status" => 1], 200);
        }    else{
            return $response->withJson(["status" => 0], 400);
        }
    });

    $app->post('/add_score_average', function ($request, $response, $args) {
        $idjadwal = $request -> getParam('idjadwal');

        $sth = $this->db->prepare("INSERT INTO `tabel_score_average`(`id_jadwal`, `id_user`, `avg_score`) SELECT a.id_jadwal, a.id_user, AVG(b.nilai) FROM tabel_pelatihan_user AS a INNER JOIN tabel_nilai AS b ON a.id_pelatihan = b.id_pelatihan WHERE a.id_jadwal= :idjadwal GROUP BY a.id_user");
        $sth ->bindParam(':idjadwal',$idjadwal);
        if($sth->execute()){
            return $response->withJson(["status" => 1], 200);
        }    else{
            return $response->withJson(["status" => 0], 400);
        }
    });

    $app->post('/update_nilai', function ($request, $response, $args) {
        $idpelatihan = $request -> getParam('idpelatihan');
        $idjadwaldetail = $request -> getParam('idjadwaldetail');
        $nilai = $request -> getParam('nilai');

        $sth = $this->db->prepare("UPDATE `tabel_nilai` SET `nilai` = :nilai WHERE id_pelatihan = :idpelatihan AND id_jadwal_detail = :idjadwaldetail");
        $sth ->bindParam(':idpelatihan',$idpelatihan);
        $sth ->bindParam(':idjadwaldetail',$idjadwaldetail);
        $sth ->bindParam(':nilai',$nilai);
        if($sth->execute()){
            return $response->withJson(["status" => 1], 200);
        }    else{
            return $response->withJson(["status" => 0], 400);
        }
    });

    $app->post('/update_score_average', function ($request, $response, $args) {
        $idjadwal = $request -> getParam('idjadwal');
        $iduser = $request -> getParam('iduser');

        $sth = $this->db->prepare("UPDATE `tabel_score_average` SET `avg_score` = (SELECT AVG(b.nilai) FROM tabel_pelatihan_user AS a INNER JOIN tabel_nilai AS b ON a.id_pelatihan = b.id_pelatihan WHERE a.id_jadwal = :idjadwal AND a.id_user = :iduser) WHERE id_jadwal = :idjadwal AND id_user = :iduser");
        $sth ->bindParam(':idjadwal',$idjadwal);
        $sth ->bindParam(':iduser',$iduser);
        if($sth->execute()){
            return $response->withJson(["status" => 1], 200);
        }    else{
            return $response->withJson(["status" => 0], 400);
        }
    });

    $app->post('/check_nilai', function ($request, $response, $args) {
        $idpelatihan  = $request ->getParam('idpelatihan');
        $idjadwaldetail  = $request ->getParam('idjadwaldetail');
        $sth = $this->db->prepare("SELECT EXISTS(SELECT * FROM tabel_nilai WHERE id_pelatihan = :idpelatihan AND id_jadwal_detail = :idjadwaldetail) AS status");
        $sth ->bindParam('idpelatihan',$idpelatihan);
        $sth ->bindParam('idjadwaldetail',$idjadwaldetail);
        $sth->execute();
        $datas = $sth->fetchAll();
        return $this->response->withJson($datas[0]);
    });

    $app->post('/check_score_average', function ($request, $response, $args) {
        $idjadwal = $request -> getParam('idjadwal');
        $iduser = $request -> getParam('iduser');

        $sth = $this->db->prepare("SELECT EXISTS(SELECT * FROM `tabel_score_average` WHERE id_jadwal = :idjadwal AND id_user = :iduser) AS status");
        $sth ->bindParam(':idjadwal',$idjadwal);
        $sth ->bindParam(':iduser',$iduser);
        $sth->execute();
        $datas = $sth->fetchAll();
        return $this->response->withJson($datas[0]);
    });

    $app->post('/add_presensi_ujian', function ($request, $response, $args) {
        $idjadwaldetail = $request -> getParam('idjadwaldetail');
        $iduser = $request -> getParam('iduser');
        $statuspresensi = $request -> getParam('statuspresensi');

        $sth = $this->db->prepare("INSERT INTO `tabel_presensi`(`id_jadwal_detail`, `id_user`, `status_presensi`) VALUES (:idjadwaldetail, :iduser, :statuspresensi)");
        $sth ->bindParam(':idjadwaldetail',$idjadwaldetail);
        $sth ->bindParam(':iduser',$iduser);
        $sth ->bindParam(':statuspresensi',$statuspresensi);
        if($sth->execute()){
            return $response->withJson(["status" => 1], 200);
        }    else{
            return $response->withJson(["status" => 0], 400);
        }
    });

    $app->post('/update_presensi_ujian', function ($request, $response, $args) {
        $idjadwaldetail = $request -> getParam('idjadwaldetail');        
        $iduser = $request-> getParam('iduser');
        $statuspresensi = $request-> getParam('statuspresensi');
        $sth = $this->db->prepare("UPDATE `tabel_presensi` SET `id_jadwal_detail` = :idjadwaldetail, `id_user` = :iduser, `status_presensi` = :statuspresensi WHERE id_jadwal_detail = :idjadwaldetail AND id_user = :iduser");
        $sth ->bindParam(':iduser',$iduser);
        $sth ->bindParam(':idjadwaldetail',$idjadwaldetail);
        $sth ->bindParam(':statuspresensi',$statuspresensi);

        if($sth->execute()){
            return $response->withJson(["status" => 1], 200);
        }    else{
            return $response->withJson(["status" => 0], 400);
        }
    });

    $app->post('/check_presensi', function ($request, $response, $args) {
        $idjadwaldetail  = $request ->getParam('idjadwaldetail');
        $iduser  = $request ->getParam('iduser');
        $sth = $this->db->prepare("SELECT EXISTS(SELECT * FROM tabel_presensi WHERE id_jadwal_detail = :idjadwaldetail AND id_user = :iduser) AS status");
        $sth ->bindParam('idjadwaldetail',$idjadwaldetail);
        $sth ->bindParam('iduser',$iduser);
        $sth->execute();
        $datas = $sth->fetchAll();
        return $this->response->withJson($datas[0]);
    });
    
    // $app->post('/add_tabel_nilai', function ($request, $response, $args) {
    //     $iduser = $request -> getParam('idpelatihan');
    //     $nilai =  $request -> getParam('nilai');
    //     $sth = $this->db->prepare("call addnilai(:idpelatihan, :nilai)");
    //     $sth ->bindParam(':idpelatihan',$iduser);
    //     $sth ->bindParam(':nilai',$nilai);

    //     if($sth->execute()){
    //         return $response->withJson(["status" => 1], 200);
    //     }    else{
    //         return $response->withJson(["status" => 0], 400);
    //     }
    // });
    
    $app->post('/registertki', function ($request, $response) {
        $username = $request -> getParam('username'); 
        $password = $request -> getParam('password');
        $nama = $request -> getParam('nama');
        $nopassport = $request -> getParam('no_passport');
        $noktp = $request -> getParam('no_ktp');
        $tempatlahir = $request -> getParam('tempatlahir');
        $tanggallahir = $request -> getParam('tanggallahir');
        $kewarganegaraan = $request -> getParam('kewarganegaraan');
        $jeniskelamin = $request -> getParam('jeniskelamin');
        $alamat = $request -> getParam('alamat');
        $notelp = $request -> getParam('notelp');
        $passfoto = $request -> getParam('passfoto');
        $ttdfoto = $request -> getParam('ttdfoto');

        $sth = $this->db->prepare("INSERT INTO tbl_user
        (year, username, password, nama, no_passport, no_ktp, tempatlahir, tanggallahir, kewarganegaraan, 
        jeniskelamin, alamat, notelp, passfoto, ttdfoto)
        VALUES (year(now()), :username, :password, :nama, :no_passport, :no_ktp, :tempatlahir, :tanggallahir, :kewarganegaraan, :jeniskelamin,
        :alamat, :notelp, :passfoto, :ttdfoto)");

        $sth ->bindParam(':username',$username);
        $sth ->bindParam(':password',$password);
        $sth ->bindParam(':nama',$nama);
        $sth ->bindParam(':no_passport',$nopassport);
        $sth ->bindParam(':no_ktp',$noktp);
        $sth ->bindParam(':tempatlahir',$tempatlahir);
        $sth ->bindParam(':tanggallahir',$tanggallahir);
        $sth ->bindParam(':kewarganegaraan',$kewarganegaraan);
        $sth ->bindParam(':jeniskelamin',$jeniskelamin);
        $sth ->bindParam(':alamat',$alamat);
        $sth ->bindParam(':notelp',$notelp);
        $sth ->bindParam(':passfoto',$passfoto);
        $sth ->bindParam(':ttdfoto',$ttdfoto);
        if($sth->execute()){
            return $response->withJson(["status" => 1], 200);
        }    else{
            return $response->withJson(["status" => 0], 400);
        }
    });

    $app->post('/registerpelatih', function ($request, $response) {
        $nama_pelatih = $request -> getParam('nama_pelatih');
        $username = $request -> getParam('username');
        $password = $request -> getParam('password');
        $sth = $this->db->prepare("INSERT INTO tabel_pelatih (year, nama_pelatih, username, password) VALUES (year(now()), :nama_pelatih, :username, :password)");
        $sth ->bindParam(':nama_pelatih',$nama_pelatih);
        $sth ->bindParam(':username',$username);
        $sth ->bindParam(':password',$password);
        if($sth->execute()){
            return $response->withJson(["status" => 1], 200);
        }    else{
            return $response->withJson(["status" => 0], 400);
        }
    });

    $app->post('/registerpegawai', function ($request, $response) {
        $nama_pegawai = $request -> getParam('nama_pegawai');
        $username = $request -> getParam('username');
        $password = $request -> getParam('password');
        $nip = $request -> getParam('nip');
        $sth = $this->db->prepare("INSERT INTO tabel_pegawai (year, nama_pegawai, username, password, nip) VALUES (year(now()), :nama_pegawai, :username, :password, :nip)");
        $sth ->bindParam(':nama_pegawai',$nama_pegawai);
        $sth ->bindParam(':username',$username);
        $sth ->bindParam(':password',$password);
        $sth ->bindParam(':nip',$nip);
        if($sth->execute()){
            return $response->withJson(["status" => 1], 200);
        }    else{
            return $response->withJson(["status" => 0], 400);
        }
    });
    
    $app->get('/getuserdata', function ($request, $response, $args) {
        $sth = $this->db->prepare("SELECT a.id_user AS iduser, a.nama, b.datecreated FROM tbl_user AS a INNER JOIN tabel_doc_user AS b ON a.id_user = b.iduser WHERE b.status=0");
        $sth->execute();
        $datas = $sth->fetchAll();
        return $this->response->withJson($datas);
    });

    $app->post('/getuserdocument', function ($request, $response, $args) {
        $iduser= $request -> getParam('iduser');        
        $sth = $this->db->prepare("SELECT IFNULL(a.scan_ktp,'') AS scanktp, IFNULL(a.scan_kompensasi,'') AS scankompensasi, IFNULL(a.scan_surat_kesehatan,'') AS scansuratkesehatan, IFNULL(a.scan_surat_kerja,'') AS scansuratkerja FROM tabel_doc_user AS a WHERE iduser = :iduser");
        $sth ->bindParam(':iduser',$iduser);
        $sth->execute();
        $datas = $sth->fetchAll();
        return $this->response->withJson($datas[0]);
    });
    
    $app->post('/approveuserdocument', function ($request, $response, $args) {
        $iduser = $request -> getParam('iduser');        
        $idpegawai = $request->getParam('idpegawai');
        $status = $request->getParam('status');
        $sth = $this->db->prepare("UPDATE `tabel_doc_user` SET id_pegawai = :idpegawai, status = :status WHERE iduser = :iduser");
        $sth ->bindParam(':iduser',$iduser);
        $sth ->bindParam(':idpegawai',$idpegawai);
        $sth ->bindParam(':status',$status);
        if($sth->execute()){
            return $response->withJson(["status" => 1], 200);
        }    else{
            return $response->withJson(["status" => 0], 400);
        }
    });

    $app->get('/getuserpaymentdata', function ($request, $response, $args) {
        $iduser  = $request ->getParam('id');
        $sth = $this->db->prepare("SELECT a.id_user AS iduser, a.nama, b.datecreated FROM tbl_user AS a INNER JOIN tabel_pembayaran AS b ON a.id_user = b.id_user WHERE b.status=0");
        $sth ->bindParam('id',$iduser);
        $sth->execute();
        $datas = $sth->fetchAll();
        return $this->response->withJson($datas);
    });

    $app->post('/getuserpayment', function ($request, $response, $args) {
        $iduser= $request -> getParam('iduser');        
        $sth = $this->db->prepare("SELECT IFNULL(a.bukti_tf,'') AS img_bukti FROM tabel_pembayaran AS a WHERE id_user = :iduser");
        $sth ->bindParam(':iduser',$iduser);
        $sth->execute();
        $datas = $sth->fetchAll();
        return $this->response->withJson($datas[0]);
    });
    
    $app->post('/approveuserpayment', function ($request, $response, $args) {
        $iduser= $request -> getParam('iduser');        
        $idpegawai = $request->getParam('idpegawai');
        $status = $request->getParam('status');
        $sth = $this->db->prepare("UPDATE `tabel_pembayaran` SET id_pegawai = :idpegawai, status = :status WHERE id_user = :iduser");
        $sth ->bindParam(':iduser',$iduser);  
        $sth ->bindParam(':idpegawai',$idpegawai);
        $sth ->bindParam(':status',$status);
        if($sth->execute()){
            return $response->withJson(["status" => 1], 200);
        }    else{
            return $response->withJson(["status" => 0], 400);
        }
    });

    $app->get('/get_pengiriman_tki', function ($request, $response, $args) {
        $sth = $this->db->prepare("SELECT id_user AS id, year, nama, status FROM tbl_user WHERE status = 1");
        $sth->execute();
        $datas = $sth->fetchAll();
        return $this->response->withJson($datas);
    });

    
    // $app->post('/update_pengiriman_by_average', function ($request, $response, $args) {
    //     $idjadwal = $request -> getParam('idjadwal');
    //     $iduser = $request -> getParam('iduser');

    //     $sth = $this->db->prepare("SELECT AVG(avg_score) FROM `tabel_score_average` WHERE id_jadwal = :idjadwal AND id_user = :iduser AND AVG(avg_score) > 75");
    //     $sth ->bindParam(':idjadwal',$idjadwal);
    //     $sth ->bindParam(':iduser',$iduser);
    //     $sth->execute();
    //     $datas = $sth->fetchAll();
    //     return $this->response->withJson($datas[0]);
    // });

    $app->get('/getdatatkiuser', function ($request, $response, $args) {
        $sth = $this->db->prepare("SELECT id_user AS iduser, nama, date_created AS datecreated FROM tbl_user");
        $sth->execute();
        $datas = $sth->fetchAll();
        return $this->response->withJson($datas);
    });
    
    $app->get('/searchdatatkiuser/{nama}', function ($request, $response, $args) {
        $sth = $this->db->prepare("SELECT id_user AS iduser, nama, date_created AS datecreated FROM tbl_user WHERE nama LIKE :nama");
        $nama = "%".$args['nama']."%";
        $sth->bindParam("nama", $nama);
        $sth->execute();
        $datas = $sth->fetchAll();
        return $this->response->withJson($datas);
    });

    $app->post('/getdetaildatatki', function ($request, $response, $args) {
        $iduser= $request -> getParam('iduser'); 
        $sth = $this->db->prepare("SELECT a.*, COALESCE(b.scan_ktp,'') AS scan_ktp, COALESCE(b.scan_kompensasi,'') AS scan_kompensasi, COALESCE(b.scan_surat_kesehatan,'') AS scan_surat_kesehatan, COALESCE(b.scan_surat_kerja,'') AS scan_surat_kerja FROM tbl_user AS a INNER JOIN tabel_doc_user AS b ON a.id_user = b.iduser WHERE a.id_user = :iduser");
        $sth ->bindParam(':iduser',$iduser);
        $sth->execute();
        $datas = $sth->fetchAll();
        return $this->response->withJson($datas[0]);
    });

    $app->post('/profile_user', function ($request, $response, $args) {
        $iduser = $request ->getParam('id_user');
        $sth = $this->db->prepare("SELECT year, username, nama, no_passport, no_ktp, tempatlahir, tanggallahir, kewarganegaraan, 
        jeniskelamin, alamat, notelp, passfoto, status FROM tbl_user WHERE id_user = :id_user");
        $sth ->bindParam('id_user',$iduser);
        $sth->execute();
        $datas = $sth->fetchAll();
        return $this->response->withJson($datas[0]);
    });

    $app->post('/profile_pelatih', function ($request, $response, $args) {
        $idpelatih = $request ->getParam('id_pelatih');      
        $sth = $this->db->prepare("SELECT year, nama_pelatih, username, foto FROM tabel_pelatih WHERE id_pelatih = :id_pelatih");
        $sth ->bindParam('id_pelatih',$idpelatih);
        $sth->execute();
        $datas = $sth->fetchAll();
        return $this->response->withJson($datas[0]);
    });

    $app->post('/update_foto_pelatih', function ($request, $response, $args) {
        $idpelatih = $request ->getParam('id_pelatih');      
        $foto = $request ->getParam('foto'); 
        $sth = $this->db->prepare("UPDATE tabel_pelatih SET foto = :foto WHERE id_pelatih = :id_pelatih");
        $sth ->bindParam('id_pelatih',$idpelatih);
        $sth ->bindParam('foto',$foto);
        if($sth->execute()){
            return $response->withJson(["status" => 1], 200);
        }    else{
            return $response->withJson(["status" => 0], 400);
        }
    });
    
    $app->post('/profile_pegawai', function ($request, $response, $args) {
        $idpegawai = $request ->getParam('id_pegawai');      
        $sth = $this->db->prepare("SELECT year, nama_pegawai, username, nip, foto FROM tabel_pegawai WHERE id_pegawai = :id_pegawai");
        $sth ->bindParam('id_pegawai',$idpegawai);
        $sth->execute();
        $datas = $sth->fetchAll();
        return $this->response->withJson($datas[0]);
    });

    $app->post('/update_foto_pegawai', function ($request, $response, $args) {
        $idpegawai = $request ->getParam('id_pegawai');      
        $foto = $request ->getParam('foto'); 
        $sth = $this->db->prepare("UPDATE tabel_pegawai SET foto = :foto WHERE id_pegawai = :id_pegawai");
        $sth ->bindParam('id_pegawai',$idpegawai);
        $sth ->bindParam('foto',$foto);
        if($sth->execute()){
            return $response->withJson(["status" => 1], 200);
        }    else{
            return $response->withJson(["status" => 0], 400);
        }
    });
};
