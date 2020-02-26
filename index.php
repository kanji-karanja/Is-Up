<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Home - Is UP?</title>
    <meta name="twitter:card" content="summary">
    <meta name="twitter:description" content="Check if a website or server is up or is Down">
    <meta property="og:description" content="Check if a website or server is up or is Down">
    <meta name="description" content="Check if a website or server is up or is Down">
    <meta property="og:title" content="Is UP?">
    <meta name="twitter:title" content="Is UP?">
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato:300,400,700,300italic,400italic,700italic">
    <link rel="stylesheet" href="assets/fonts/font-awesome.min.css">
</head>

<body>
    <nav class="navbar navbar-light navbar-expand bg-light navigation-clean">
        <div class="container"><a class="navbar-brand" href="#">Is UP?</a><button data-toggle="collapse" class="navbar-toggler" data-target="#navcol-1"></button>
            <div class="collapse navbar-collapse" id="navcol-1"><a class="btn btn-primary ml-auto" role="button" href="#">Sign In</a></div>
        </div>
    </nav>
    <header class="masthead text-white text-center" style="background: url('assets/img/bg-masthead.jpg')no-repeat center center;background-size: cover;background-image: url(&quot;assets/img/woman-holding-laptop-beside-glass-wall-1181316.jpg&quot;);height: 794px;">
        <div class="overlay"></div>
        <div class="container">
            <div class="row">
                <div class="col-xl-9 mx-auto">
                    <h1 class="mb-5">Easily check if your Website or Server is Up or Down</h1>
                </div>
                <div class="col-md-10 col-lg-8 col-xl-7 mx-auto">
                        <div class="form-row">
                            <div class="col-12 col-md-9 mb-2 mb-md-0"><input class="form-control form-control-lg" type="url" id="gottenUrl" placeholder="Enter your URL" required></div>
                            <div class="col-12 col-md-3"><button class="btn btn-primary btn-block btn-lg"  onclick="checkStatus()">Check</button></div>
                        </div>
                </div>
            </div>
        </div>
    </header>
    <footer class="footer bg-light" style="height: 5px;">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 my-auto h-100 text-center">
                    <p class="align-items-center align-content-center text-muted small mb-4 mb-lg-0">© Is UP? 2020. All Rights Reserved. Made with ♥ by&nbsp;<a href="https://cryosoft.co.ke">Cryosoft Corporation</a></p>
                </div>
            </div>
        </div>
    </footer>
    <div class="modal fade" role="dialog" tabindex="-1" id="detailsShow">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="url">{{url}}</h4><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button></div>
                <div class="modal-body">
                    <div class="align-items-center" id="status"></div>
                    
                </div>
                <div class="modal-footer"><button class="btn btn-light" type="button" data-dismiss="modal">Close</button></div>
            </div>
        </div>
    </div>
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <script>
         let isChecked =false;
         let myTimer;
        const checkStatus=()=>{
        let url =document.querySelector("#gottenUrl").value;
        let xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("url").innerHTML = url;
                document.getElementById("status").innerHTML = this.responseText;
                if(!isChecked){
                    timer();
                    $("#detailsShow").modal();
                    isChecked=true;
                }
            }
        };
        xmlhttp.open("GET", "checker.php?url=" + url, true);
        xmlhttp.send();
    }
    const timer=()=>{
        myTimer = setInterval(function(){
       checkStatus();}, 5000);
    }
    const stopTimer=()=>{
        isChecked=false;
        clearInterval(myTimer);
    }
    $("#detailsShow").on('hidden.bs.modal', function(){
        stopTimer();
  });
    </script>
</body>
</html>