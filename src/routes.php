<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

return function (App $app) {
    $container = $app->getContainer();

    $app->get('/aa/[{name}]', function (Request $request, Response $response, array $args) use ($container) {
        // Sample log message
        $container->get('logger')->info("Slim-Skeleton '/' route");
        // Render index view
        return $container->get('renderer')->render($response, 'index.phtml', $args);
    });

    $app->post('/loginuser', function ($request, $response, $args) {
        $username = $request -> getParam('username');
        $password = $request -> getParam('password');

        $sth = $this->db->prepare("SELECT a.id_user as iduser,a.username as username FROM tbl_user AS a WHERE a.username = :username AND a.password = :password");
        $sth ->bindParam(':username',$username);
        $sth ->bindParam(':password',$password);
        $sth->execute();
        $datas = $sth->fetchAll();
        return $this->response->withJson($datas[0]);
    });

    $app->post('/loginpegawai', function ($request, $response, $args) {
        $username = $request -> getParam('username');
        $password = $request -> getParam('password');

        $sth = $this->db->prepare("SELECT a.id_pegawai as iduser,a.username as username FROM tabel_pegawai AS a WHERE a.username = :username AND a.password = :password");
        $sth ->bindParam(':username',$username);
        $sth ->bindParam(':password',$password);
        $sth->execute();
        $datas = $sth->fetchAll();
        return $this->response->withJson($datas[0]);
    });

    $app->post('/loginpelatih', function ($request, $response, $args) {
        $username = $request -> getParam('username');
        $password = $request -> getParam('password');

        $sth = $this->db->prepare("SELECT a.id_pelatih as iduser,a.username as username FROM tabel_pelatih AS a WHERE a.username = :username AND a.password = :password");
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
        if($buktipembayaran == ""){
            $buktipembayaran = null;
        }
        $sth = $this->db->prepare("UPDATE `tabel_pembayaran` SET `bukti_tf`=COALESCE(:buktipembayaran,bukti_tf) WHERE id_user = :iduser");
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

    
      
    $app->post('/registertki', function ($request, $response) {
        $username = $request -> getParam('username');
        $password = $request -> getParam('password');
        $nama = $request -> getParam('nama');
        $nopassport = $request -> getParam('no_passport');
        $noktp = $request -> getParam('no_ktp');
        $tempatlahir = $request -> getParam('tempatlahir');
        $tanggallahir = $request -> getParam('tanggallahir');
        $umur = $request -> getParam('umur');
        $kewarganegaraan = $request -> getParam('kewarganegaraan');
        $jeniskelamin = $request -> getParam('jeniskelamin');
        $alamat = $request -> getParam('alamat');
        $notelp = $request -> getParam('no_telp');
        $notelpalternative = $request -> getParam('no_telp_alternative');
        $statuspernikahan = $request -> getParam('status_pernikahan');
        $tinggibadan = $request -> getParam('tinggi_badan');
        $beratbadan = $request -> getParam('berat_badan');
        $matakiri = $request -> getParam('matakiri');
        $matakanan = $request -> getParam('matakanan');
        $butawarna = $request -> getParam('buta_warna');
        $upahyangdiinginkan = $request -> getParam('upahyangdiinginkan');
        $sektor1 = $request -> getParam('sektor1');
        $sektor2 = $request -> getParam('sektor2');
        $sektor3 = $request -> getParam('sektor3');
        $pekerjaan1 = $request -> getParam('pekerjaan1');
        $pekerjaan2 = $request -> getParam('pekerjaan2');
        $pekerjaan3 = $request -> getParam('pekerjaan3');
        $lokasi1 = $request -> getParam('lokasi1');
        $lokasi2 = $request -> getParam('lokasi2');
        $lokasi3 = $request -> getParam('lokasi3');
        $pendidikanterakhir = $request -> getParam('pendidikanterakhir');
        $bidang = $request -> getParam('bidang');
        $mandarin = $request -> getParam('mandarin');
        $inggris = $request -> getParam('inggris');
        $bahasalain = $request -> getParam('bahasa_lain');
        $sertifikatkerja1 = $request -> getParam('sertifikatkerja1');
        $sertifikatkerja2 = $request -> getParam('sertifikatkerja2');
        $sertifikatkerja3 = $request -> getParam('sertifikatkerja3');
        $pengalamankerja1 = $request -> getParam('pengalamankerja1');
        $pengalamankerjatanggalmulai1 = $request -> getParam('pengalamankerjatanggalmulai1');
        $pengalamankerjaselesai1 = $request -> getParam('pengalamankerjaselesai1');
        $pengalamankerja2 = $request -> getParam('pengalamankerja2');
        $pengalamankerjatanggalmulai2 = $request -> getParam('pengalamankerjatanggalmulai2');
        $pengalamankerjaselesai2 = $request -> getParam('pengalamankerjaselesai2');
        $hasilmedicalcheckup = $request -> getParam('hasilmedicalcheckup');
        $tanggalmedicalcheck = $request -> getParam('tanggalmedicalcheck');
        $klinikmedicalcheck = $request -> getParam('klinikmedicalcheck');
        $pendidikanbahasamandarin = $request -> getParam('pendidikanbahasamandarin');
        $namalembagapendidikan = $request -> getParam('namalembagapendidikan');
        $tglmulaipendidikanmandarin = $request -> getParam('tglmulaipendidikanmandarin');
        $tglselesaipendidikanmandarin = $request -> getParam('tglselesaipendidikanmandarin');
        $passfoto = $request -> getParam('passfoto');
        $ttdfoto = $request -> getParam('ttdfoto');

        $sth = $this->db->prepare("INSERT INTO tbl_user
        (username, password, nama, no_passport, no_ktp, tempatlahir, tanggallahir, umur, kewarganegaraan, 
        jeniskelamin, alamat, no_telp, no_telp_alternative, status_pernikahan, tinggi_badan, berat_badan, 
        matakiri, matakanan, buta_warna, upahyangdiinginkan, sektor1, sektor2, sektor3, pekerjaan1, pekerjaan2, pekerjaan3,
        lokasi1, lokasi2, lokasi3, pendidikanterakhir, bidang, mandarin, inggris, bahasa_lain,
        sertifikatkerja1, sertifikatkerja2, sertifikatkerja3, pengalamankerja1, pengalamankerjatanggalmulai1,
        pengalamankerjaselesai1, pengalamankerja2, pengalamankerjatanggalmulai2, pengalamankerjaselesai2,
        hasilmedicalcheckup, tanggalmedicalcheck, klinikmedicalcheck, pendidikanbahasamandarin, namalembagapendidikan,
        tglmulaipendidikanmandarin, tglselesaipendidikanmandarin, passfoto, ttdfoto)
        VALUES (:username, :password, :nama, :no_passport, :no_ktp, :tempatlahir, :tanggallahir, :umur, :kewarganegaraan, :jeniskelamin,
        :alamat, :no_telp, :no_telp_alternative, :status_pernikahan, :tinggi_badan, :berat_badan, :matakiri, :matakanan, :buta_warna,
        :upahyangdiinginkan, :sektor1, :sektor2, :sektor3, :pekerjaan1, :pekerjaan2, :pekerjaan3, :lokasi1, :lokasi2, :lokasi3,
        :pendidikanterakhir, :bidang, :mandarin, :inggris, :bahasa_lain, :sertifikatkerja1, :sertifikatkerja2, :sertifikatkerja3,
        :pengalamankerja1, :pengalamankerjatanggalmulai1, :pengalamankerjaselesai1, :pengalamankerja2, :pengalamankerjatanggalmulai2,
        :pengalamankerjaselesai2, :hasilmedicalcheckup, :tanggalmedicalcheck, :klinikmedicalcheck, :pendidikanbahasamandarin,
        :namalembagapendidikan, :tglmulaipendidikanmandarin, :tglselesaipendidikanmandarin, :passfoto, :ttdfoto)");

        $sth ->bindParam(':username',$username);
        $sth ->bindParam(':password',$password);
        $sth ->bindParam(':nama',$nama);
        $sth ->bindParam(':no_passport',$nopassport);
        $sth ->bindParam(':no_ktp',$noktp);
        $sth ->bindParam(':tempatlahir',$tempatlahir);
        $sth ->bindParam(':tanggallahir',$tanggallahir);
        $sth ->bindParam(':umur',$umur);
        $sth ->bindParam(':kewarganegaraan',$kewarganegaraan);
        $sth ->bindParam(':jeniskelamin',$jeniskelamin);
        $sth ->bindParam(':alamat',$alamat);
        $sth ->bindParam(':no_telp',$notelp);
        $sth ->bindParam(':no_telp_alternative',$notelpalternative);
        $sth ->bindParam(':status_pernikahan',$statuspernikahan);
        $sth ->bindParam(':tinggi_badan',$tinggibadan);
        $sth ->bindParam(':berat_badan',$beratbadan);
        $sth ->bindParam(':matakiri',$matakiri);
        $sth ->bindParam(':matakanan',$matakanan);
        $sth ->bindParam(':buta_warna',$butawarna);
        $sth ->bindParam(':upahyangdiinginkan',$upahyangdiinginkan);
        $sth ->bindParam(':sektor1',$sektor1);
        $sth ->bindParam(':sektor2',$sektor2);
        $sth ->bindParam(':sektor3',$sektor3);
        $sth ->bindParam(':pekerjaan1',$pekerjaan1);
        $sth ->bindParam(':pekerjaan2',$pekerjaan2);
        $sth ->bindParam(':pekerjaan3',$pekerjaan3);
        $sth ->bindParam(':lokasi1',$lokasi1);
        $sth ->bindParam(':lokasi2',$lokasi2);
        $sth ->bindParam(':lokasi3',$lokasi3);
        $sth ->bindParam(':pendidikanterakhir',$pendidikanterakhir);
        $sth ->bindParam(':bidang',$bidang);
        $sth ->bindParam(':mandarin',$mandarin);
        $sth ->bindParam(':inggris',$inggris);
        $sth ->bindParam(':bahasa_lain',$bahasalain);
        $sth ->bindParam(':sertifikatkerja1',$sertifikatkerja1);
        $sth ->bindParam(':sertifikatkerja2',$sertifikatkerja2);
        $sth ->bindParam(':sertifikatkerja3',$sertifikatkerja3);
        $sth ->bindParam(':pengalamankerja1',$pengalamankerja1);
        $sth ->bindParam(':pengalamankerjatanggalmulai1',$pengalamankerjatanggalmulai1);
        $sth ->bindParam(':pengalamankerjaselesai1',$pengalamankerjaselesai1);
        $sth ->bindParam(':pengalamankerja2',$pengalamankerja2);
        $sth ->bindParam(':pengalamankerjatanggalmulai2',$pengalamankerjatanggalmulai2);
        $sth ->bindParam(':pengalamankerjaselesai2',$pengalamankerjaselesai2);
        $sth ->bindParam(':hasilmedicalcheckup',$hasilmedicalcheckup);
        $sth ->bindParam(':tanggalmedicalcheck',$tanggalmedicalcheck);
        $sth ->bindParam(':klinikmedicalcheck',$klinikmedicalcheck);
        $sth ->bindParam(':pendidikanbahasamandarin',$pendidikanbahasamandarin);
        $sth ->bindParam(':namalembagapendidikan',$namalembagapendidikan);
        $sth ->bindParam(':tglmulaipendidikanmandarin',$tglmulaipendidikanmandarin);
        $sth ->bindParam(':tglselesaipendidikanmandarin',$tglselesaipendidikanmandarin);
        $sth ->bindParam(':passfoto',$passfoto);
        $sth ->bindParam(':ttdfoto',$ttdfoto);
        if($sth->execute()){
            return $response->withJson(["status" => 1], 200);
        }    else{
            return $response->withJson(["status" => 0], 400);
        }
    });

    $app->post('/registerpelatih', function ($request, $response) {
        $username = $request -> getParam('username');
        $password = $request -> getParam('password');
        $nama_pelatih = $request -> getParam('nama_pelatih');
        $sth = $this->db->prepare("INSERT INTO tabel_pelatih (nama_pelatih, username, password) VALUES (:nama_pelatih, :username, :password)");
        if($sth->execute()){
            return $response->withJson(["status" => 1], 200);
        }    else{
            return $response->withJson(["status" => 0], 400);
        }
    });

    $app->post('/registerpegawai', function ($request, $response) {
        $username = $request -> getParam('username');
        $password = $request -> getParam('password');
        $nama_pelatih = $request -> getParam('nama_pegawai');
        $nip = $request -> getParam('nip');
        $sth = $this->db->prepare("INSERT INTO tabel_pegawai (nama, username, password, nip) VALUES (:nama_pelatih, :username, :password, :nip)");
        if($sth->execute()){
            return $response->withJson(["status" => 1], 200);
        }    else{
            return $response->withJson(["status" => 0], 400);
        }
    });

};
