<?php
include_once "includes/function/func.php";
$filename = dirname($_SERVER['SCRIPT_FILENAME'])."/247.txt";
$msg = file_get_contents($filename);
echo strlen($msg);
$struct = TextMsgParse($msg);
print_r($struct);exit;
//echo $struct['text'];
MsgParse($filename, $_REQUEST['showpart']);
//print_r($struct['images']);
exit;
$hdrs = 'Received: (qmail 27839 invoked by uid 110); 27 Feb 2012 07:42:50 -0600
Delivered-To: 2-ddf+kolygm@tickletrain.com
Received: (qmail 27836 invoked from network); 27 Feb 2012 07:42:50 -0600
Received: from mail-lpp01m010-f47.google.com (209.85.215.47)
  by mail.tickletrain.com with (RC4-SHA encrypted) SMTP; 27 Feb 2012 07:42:48 -0600
Received: by lagw12 with SMTP id w12so613544lag.6
        for <ddf+kolygm@tickletrain.com>; Mon, 27 Feb 2012 05:42:46 -0800 (PST)
Return-Path: <oleg@webteam.pro>
Received-SPF: pass (google.com: domain of oleg@webteam.pro designates 10.152.110.102 as permitted sender) client-ip=10.152.110.102;
Authentication-Results: mr.google.com; spf=pass (google.com: domain of oleg@webteam.pro designates 10.152.110.102 as permitted sender) smtp.mail=oleg@webteam.pro; dkim=pass header.i=oleg@webteam.pro
Received: from mr.google.com ([10.152.110.102])
        by 10.152.110.102 with SMTP id hz6mr12450049lab.21.1330350166648 (num_hops = 1);
        Mon, 27 Feb 2012 05:42:46 -0800 (PST)
DKIM-Signature: v=1; a=rsa-sha256; c=relaxed/relaxed;
        d=webteam.pro; s=webteam;
        h=mime-version:x-originating-ip:x-goomoji-body:date:message-id
         :subject:from:to:cc:content-type;
        bh=C7jrDamCey1xZIrVuwm1UFej8cWOzQFThIFzHF3CE5A=;
        b=J+gQ3KUonXqjnBZrTVL0JGG7ELoFoCCHicgwHFZF5uk73QjT83YBQXodAisOZ932Ej
         mjIwEBVSTi4rU/EYLMJJo9qAO+aLcrspBToH5JjWeGH4XKfAKi5QnjqqSaBMU0gzJNJG
         vaj4iT0nRt/1v2jaKw9Ncc83m5LuXAsFAdFSI=
MIME-Version: 1.0
Received: by 10.152.110.102 with SMTP id hz6mt12450049lab.21.1330350166557;
 Mon, 27 Feb 2012 05:42:46 -0800 (PST)
Received: by 10.112.99.102 with HTTP; Mon, 27 Feb 2012 05:42:45 -0800 (PST)
X-Originating-IP: [85.95.152.139]
X-Goomoji-Body: true
Date: Tue, 28 Feb 2012 00:42:45 +1100
Message-ID: <CAD4+Txbza6Q4SCCpsNef-k0-sLN+jkvhgNKCEGOosae7_BzJAA@mail.gmail.com>
Subject: =?KOI8-R?Q?test_mail_for_multiple_receivers_and_with_attach_=28=C1?=
	=?KOI8-R?Q?=D4=D4=C1=DE=29?=
From: Oleg Kuznetsov <oleg@webteam.pro>
To: Oleg K <kolygm@gmail.com>, =?KOI8-R?B?78zFxyDr1drOxcPP1w==?= <kkoly@mail.ru>
Cc: Oleg Kuznetsov <oleg@shadowteam.net>
Content-Type: multipart/mixed; boundary=bcaec54eee6a1b0a2c04b9f24959
Bcc: ddf+kolygm@tickletrain.com
X-Gm-Message-State: ALoCoQlkdwQ809CJWWJ4iTaWbw4oucqZg7YEkl7RdcHgBosnkT3F+rUU79OdpcoFTDc85nTiXYiJ
';
//print_r(MsgHeadersParse($hdrs));
//print_r(imap_mime_header_decode("=?KOI8-R?Q?test_mail_for_multiple_receivers_and_with_attach_=28=C1?==?KOI8-R?Q?=D4=D4=C1=DE=29?="));
//print_r(MsgAddressParse("=?iso-8859-1?Q?'Antonio_Gonz=E1lez'?= <bierzonet@gmail.com>"));
//print_r(MsgAddressParse("Antonio_Gonz <bierzonet@gmail.com>"));
?>
