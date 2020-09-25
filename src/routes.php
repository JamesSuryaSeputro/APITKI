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

        $sth = $this->db->prepare("SELECT a.id_user as iduser, a.username as username, a.nama AS nama FROM tbl_user AS a WHERE a.username = :username AND a.password = :password");
        $sth ->bindParam(':username',$username);
        $sth ->bindParam(':password',$password);
        $sth->execute();
        $datas = $sth->fetchAll();
        return $this->response->withJson($datas[0]);
    });

    $app->post('/loginpegawai', function ($request, $response, $args) {
        $username = $request -> getParam('username');
        $password = $request -> getParam('password');

        $sth = $this->db->prepare("SELECT a.id_pegawai as iduser, a.username as username, a.nama_pegawai AS nama FROM tabel_pegawai AS a WHERE a.username = :username AND a.password = :password");
        $sth ->bindParam(':username',$username);
        $sth ->bindParam(':password',$password);
        $sth->execute();
        $datas = $sth->fetchAll();
        return $this->response->withJson($datas[0]);
    });

    $app->post('/loginpelatih', function ($request, $response, $args) {
        $username = $request -> getParam('username');
        $password = $request -> getParam('password');

        $sth = $this->db->prepare("SELECT a.id_pelatih as iduser, a.username as username, a.nama_pelatih AS nama FROM tabel_pelatih AS a WHERE a.username = :username AND a.password = :password");
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
        $iduser= $request -> getParam('iduser');        
        $idsubject = $request-> getParam('idsubject');
        $hari   =  $request-> getParam('hari');
    
        $sth = $this->db->prepare("INSERT INTO `tabel_jadwal_pelatihan`(`id_subject`, `id_pelatih`, `hari`) VALUES (:idsubject,:iduser,:hari)");
        $sth ->bindParam(':iduser',$iduser);
        $sth ->bindParam(':idsubject',$idsubject);
        $sth ->bindParam(':hari',$hari);

        if($sth->execute()){
            return $response->withJson(["status" => 1], 200);
        }    else{
            return $response->withJson(["status" => 0], 400);
        }
    });
    $app->post('/get_jadwal_pelatihan_all', function ($request, $response, $args) {
        $iduser = $request -> getParam('iduser');

        $sth = $this->db->prepare("SELECT a.`id_jadwal`, a.`id_subject`,b.nama_subject, a.`hari` FROM `tabel_jadwal_pelatihan` as a INNER JOIN tabel_subject_pelatihan as b on b.id_subject = a.id_subject WHERE a.status = 1 and a.id_pelatih = :iduser");
        $sth ->bindParam(':iduser',$iduser);
        $sth->execute();
        $datas = $sth->fetchAll();
        return $this->response->withJson($datas);
    });

    $app->post('/delete_jadwal_pelatih', function ($request, $response, $args) {
        $iduser = $request -> getParam('idjadwal');

        $sth = $this->db->prepare("DELETE FROM `tabel_jadwal_pelatihan` WHERE id_jadwal = :idjadwal");
        $sth ->bindParam(':idjadwal',$iduser);

        if($sth->execute()){
            return $response->withJson(["status" => 1], 200);
        }    else{
            return $response->withJson(["status" => 0], 400);
        }
    });

    $app->post('/get_user_all', function ($request, $response, $args) {
        $idjadwal  = $request ->getParam('idjadwal');
        $sth = $this->db->prepare("SELECT c.id_user as id_user ,c.nama,c.jeniskelamin,c.date_created as tanggalterdaftar,c.passfoto FROM tbl_user as c where id_user NOT IN (SELECT a.id_user as id_user from tbl_user as a RIGHT JOIN tabel_pelatihan_user as b on b.id_user = a.id_user where b.id_jadwal = :idjadwal )");
        $sth ->bindParam('idjadwal',$idjadwal);
        $sth->execute();
        $datas = $sth->fetchAll();
        return $this->response->withJson($datas);
    });
    
    $app->post('/get_detail_user_pelatihan', function ($request, $response, $args) {
        $idjadwal = $request -> getParam('idjadwal');

        $sth = $this->db->prepare("SELECT a.id_pelatihan as id,b.nama as nama,b.date_created as tanggalterdaftar,b.jeniskelamin,b.passfoto FROM `tabel_pelatihan_user` as a INNER JOIN tbl_user as b on a.id_user = b.id_user where a.id_jadwal = :idjadwal AND a.status = 1");
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

        $sth = $this->db->prepare("SELECT a.id_pelatihan as id,b.nama as nama,b.date_created as tanggalterdaftar,b.jeniskelamin,b.passfoto,IFNULL(c.nilai,0) as nilai FROM `tabel_pelatihan_user` as a INNER JOIN tbl_user as b on a.id_user = b.id_user LEFT JOIN tabel_nilai as c on c.id_pelatihan = a.id_pelatihan where a.id_jadwal = :idjadwal AND a.status = 1");
        $sth ->bindParam(':idjadwal',$idjadwal);
        $sth->execute();
        $datas = $sth->fetchAll();
        return $this->response->withJson($datas);
    });

    $app->post('/add_tabel_nilai', function ($request, $response, $args) {
        $iduser = $request -> getParam('idpelatihan');
        $nilai =  $request -> getParam('nilai');
        $sth = $this->db->prepare("call addnilai(:idpelatihan, :nilai)");
        $sth ->bindParam(':idpelatihan',$iduser);
        $sth ->bindParam(':nilai',$nilai);

        if($sth->execute()){
            return $response->withJson(["status" => 1], 200);
        }    else{
            return $response->withJson(["status" => 0], 400);
        }
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
        $sth = $this->db->prepare("INSERT INTO tabel_pelatih (nama_pelatih, username, password) VALUES (:nama_pelatih, :username, :password)");
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
        $sth = $this->db->prepare("INSERT INTO tabel_pegawai (nama_pegawai, username, password, nip) VALUES (:nama_pegawai, :username, :password, :nip)");
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
        $iduser= $request -> getParam('iduser');        
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

    $app->post('/profile_user', function ($request, $response, $args) {
        $iduser = $request ->getParam('id_user');
        $sth = $this->db->prepare("SELECT year, username, nama, no_passport, no_ktp, tempatlahir, tanggallahir, kewarganegaraan, 
        jeniskelamin, alamat, notelp, passfoto FROM tbl_user WHERE id_user = :id_user");
        $sth ->bindParam('id_user',$iduser);
        $sth->execute();
        $datas = $sth->fetchAll();
        return $this->response->withJson($datas[0]);
    });

    $app->post('/profile_pelatih', function ($request, $response, $args) {
        $idpelatih = $request ->getParam('id_pelatih');      
        $sth = $this->db->prepare("SELECT nama_pelatih, username, foto FROM tabel_pelatih WHERE id_pelatih = :id_pelatih");
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
        $sth = $this->db->prepare("SELECT nama_pegawai, username, nip, foto FROM tabel_pegawai WHERE id_pegawai = :id_pegawai");
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
