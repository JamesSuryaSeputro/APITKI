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

    $app->get('/', function ($request, $response, $args) {
        $sth = $this->db->prepare("SELECT * FROM tabel_pegawai");
        $sth->execute();
        $datas = $sth->fetchAll();
        return $this->response->withJson($datas);
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
        $sth = $this->db->prepare("UPDATE `tabel_pembayaran` SET `bukti_tf`=:buktipembayaran WHERE id_user = :iduser");
        $sth ->bindParam(':iduser',$iduser);
        $sth ->bindParam(':buktipembayaran',$buktipembayaran);

        if($sth->execute()){
            return $response->withJson(["status" => 1], 200);
        }    else{
            return $response->withJson(["status" => 0], 400);
        }
    });

    $app->post('/registertki', function ($request, $response) {
        // get the parameter from the form submit
        $username = $request -> getParam('username');
        $password = $request -> getParam('password');
        $nama = $request -> getParam('nama');
        $nopassport = $request -> getParam('no_passport');
        $tempatlahir = $request -> getParam('tempatlahir');
        $taggallahir = $request -> getParam('tanggallahir');
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
        $butawarna = $request -> getParam('butawarna');
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
        $tanggalpendidikanbahasamandarin = $request -> getParam('tanggalpendidikanbahasamandarin');
        $passfoto = $request -> getParam('passfoto');
        $ttdfoto = $request -> getParam('ttdfoto');

        $sth = $this->db->prepare("INSERT INTO tbl_user AS a 
        (a.username, a.password, a.nama, a.no_passport AS nopassport, a.no_ktp AS noktp, a.tempatlahir, a.tanggallahir,
        a.umur, a.kewarganegaraan, a.alamat, a.no_telp AS notelp, a.no_telp_alternative AS notelpalternative,
        a.status_pernikahan AS statusnikah, a.tinggi_badan AS tinggi, a.berat_badan AS berat, a.matakiri, a.matakanan,
        a.butawarna, a.upahyangdiinginkan AS upah, a.sektor1, a.sektor2, a.sektor3, a.pekerjaan1, a.pekerjaan2, a.pekerjaan3,
        a.lokasi1, a.lokasi2, a.lokasi3, a.pendidikanterakhir, a.bidang, a.mandarin, a.inggris, a.bahasa_lain AS bahasalain,
        a.sertifikatkerja1, a.sertifikatkerja2, a.sertifikatkerja3, a.pengalamankerja1, a.pengalamankerjatanggalmulai1 AS tanggalmulai1,
        a.pengalamankerjaselesai1 AS tanggalselesai1, a.pengalamankerja2, a.pengalamankerjatanggalmulai2 AS tanggalmulai2,
        a.pengalamankerjaselesai2 AS tanggalselesai2, a.hasilmedicalcheckup AS hasilmcu, a.tanggalmedicalcheck AS tanggalmc,
        a.klinikmedicalcheck AS klinikmc, a.pendidikanbahasamandarin AS pendidikanmandarin, a.namalembagapendidikan AS namalembaga,
        a.tanggalpendidikanbahasamandarin AS tglpendidikanmandarin, a.passfoto, a.ttdfoto)
        VALUES (:username, :password, :nama, :no_passport, :no_ktp, :tempatlahir, :tanggallahir, :umur, :kewarganegaraan,
        :alamat, :no_telp, :no_telp_alternative, :status_pernikahan, :tinggi_badan, :berat_badan, :matakiri, :matakanan, :butawarna,
        :upahyangdiinginkan, :sektor1, :sektor2, :sektor3, :pekerjaan1, :pekerjaan2, :pekerjaan3, :lokasi1, :lokasi2, :lokasi3,
        :pendidikanterakhir, :bidang, :mandarin, :inggris, :bahasa_lain, :sertifikatkerja1, :sertifikatkerja2, :sertifikatkerja3,
        :pengalamankerja1, :pengalamankerjatanggalmulai1, :pengalamankerjaselesai1, :pengalamankerja2, :pengalamankerjatangalmulai2,
        :pengalamankerjaselesai2, :hasilmedicalcheckup, :tanggalmedicalcheck, :klinikmedicalcheck, :pendidikanbahasamandarin, :namalembagapendidikan, :tanggalpendidikanbahasamandarin, :passfoto, :ttdfoto)");
        $sth ->bindParam(':username',$username);
        $sth ->bindParam(':password',$password);
        $sth ->bindParam(':nama',$nama);
        $sth ->bindParam(':no_passport',$nopassport);
        $sth ->bindParam(':no_ktp',$noktp);
        $sth ->bindParam(':tempatlahir',$tempatlahir);
        $sth ->bindParam(':tanggallahir',$tanggallahir);
        $sth ->bindParam(':umur',$umur);
        $sth ->bindParam(':kewarganegaraan',$kewarganengaraan);
        $sth ->bindParam(':alamat',$alamat);
        $sth ->bindParam(':no_telp',$notelp);
        $sth ->bindParam(':no_telp_alternative',$notelpalternative);
        $sth ->bindParam(':status_pernikahan',$statuspernikahan);
        $sth ->bindParam(':tinggi_badan',$tinggibadan);
        $sth ->bindParam(':berat_badan',$beratbadan);
        $sth ->bindParam(':matakiri',$matakiri);
        $sth ->bindParam(':matakanan',$matakanan);
        $sth ->bindParam(':butawarna',$butawarna);
        $sth ->bindParam(':upahyangdiiinginkan',$upahyangdiinginkan);
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
        $sth ->bindParam(':tanggalpendidikanbahasamandarin',$tanggalpendidikanbahasamandarin);
        $sth ->bindParam(':passfoto',$passfoto);
        $sth ->bindParam(':ttdfoto',$ttdfoto);
        $sth->execute();
        $datas = $sth->fetchAll();
        return $this->response->withJson($datas[0]);
    });
};
