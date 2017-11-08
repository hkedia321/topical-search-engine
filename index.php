<!DOCTYPE html>
<html>
<head>
    <title>Topical Search Engine - Web Mining</title>
    <link href="assets/css/icons.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="assets/materialize/css/materialize.min.css" />
    <link rel="stylesheet" type="text/css" href="assets/css/index.css">
</head>
<body>
    <div class="top">
        <div class="top-overlay"></div>
        <div class="container">
            <br /><br/>
            <div class="row">
                <div class="card col m6 s12 offset-m3">
                    <h4>
                        <center>Search the Web</center>
                    </h4>
                    <h6 class="center">Search for keywords in the database</h6>
                    <form id="searchForm">
                        <div class="row">
                            <div class="input-field col s12 m12">
                                <input type="text" name="query" placeholder="Search..." id="query" required />
                                <label for="name"></label>
                            </div>
                            <div class="center">
                                <button class="btn waves-effect waves-light" type="submit" name="submit">Search
                                    <i class="material-icons right">send</i>
                                </button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
            <div class="row">
                <div class="card col m6 s12 offset-m3">
                    <h4>
                        <center>Crawl Web</center>
                    </h4>
                    <h6 class="center">Crawl the web and save links in database</h6>
                    <form action="crawl.php" method="get" target="_blank">
                        <div class="row">
                            <div class="input-field col s12 m12">
                                <input type="url" name="link" placeholder="Starting Link" id="link" required />
                            </div>
                            <div class="center">
                                <button class="btn waves-effect waves-light" type="submit" name="submit">Crawl
                                    <i class="material-icons right">send</i>
                                </button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
            <br/><br/>
        </div>
    </div>
    <div class="container">
        <div class="row" id="result-div">
            <h4 class="center">Search Results</h4>
            <div class="col s12 m12 card">
                <table class="table bordered striped responsive-table">
                    <thead>
                      <tr>
                        <th>no.</th>
                        <th>Link</th>
                        <th>Title</th>
                        <!-- <th>Keywords</th> -->
                    </tr>
                </thead>
                <tbody id="result">

                </tbody>
            </table>
        </div>
    </div>
    <div class="row" id="no-result">
        <h4 class="center">No Result found!</h4>
    </div>
    <div class="row center" id="loading-result">
        <img src="assets/images/loading.gif" class="img-responsive">
    </div>
</div>

<script type="text/javascript" src="assets/js/jquery.js"></script>
<script type="text/javascript" src="assets/materialize/js/materialize.min.js"></script>
<script type="text/javascript">
    $("#result-div").hide();
    $("#no-result").hide();
    $("#loading-result").hide();
    $("#searchForm").submit(function(event){
        event.preventDefault();
        fetchResults();
    })
    function fetchResults(){
        $("#loading-result").fadeIn();
        var query=document.getElementById("query").value;
        console.log(query)
        $.get( "get-result.php?query="+query, function( data ) {
            $("#loading-result").fadeOut();
            var tbody="";
            console.log(data);
            var links=[];
            var no=0;
            for(var i=0;i<data.length;i++){
                if(links.indexOf(data[i].url)==-1){
                    var tr="<tr>";
                    tr+="<td>"+(no+1)+"</td>";
                    tr+="<td><a href='"+data[i].url+"'>"+data[i].url+"</a></td>";
                    tr+="<td>"+data[i].title+"</td>";
                    //tr+="<td>"+data[i].keywords+"</td>";
                    tr+="</tr>";
                    tbody+=tr;
                    links.push(data[i].url);
                    no++;
                }
                if(no>=10) break;
            }
            if(data.length===0){
                $("#no-result").fadeIn();
            }
            else{
                $("#result-div").fadeIn();
                $("#result").html(tbody);
            }
        });
    }
</script>
</body>

</html>
