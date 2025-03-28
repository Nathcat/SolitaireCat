<!DOCTYPE html>
<html>
    <head>
        <title>SolitaireCat</title>

        <link rel="stylesheet" href="https://nathcat.net/static/css/new-common.css">
        <link rel="stylesheet" href="/static/css/home.css">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    </head>

    <body>
        <div id="page-content" class="content">
            <?php include("header.php"); ?>
            
            <div class="main">
                <div class="row align-center justify-center">
                    <h1>Welcome, <?php echo $_SESSION["user"]["fullName"]; ?></h1>
                </div>

                <div class="row align-center justify-center big-button" onclick="location = '/game';">
                    <h2>Play classic</h2>
                </div>

                <div id="timer-leaderboard" class="leaderboard"><h2>Fastest solve times</h2></div>
                <div id="solved-leaderboard" class="leaderboard"><h2>Most games won</h2></div>
            </div>

            <script>
                fetch("https://data.nathcat.net/data/get-leaderboard-state.php", {
                    method: "POST",
                    body: JSON.stringify({
                        "leaderboardId": 2,
                        "orderBy": "ASC",
                        "limit": 5
                    })
                }).then((r) => r.json()).then((r) => {
                    if (r.status === "success") {
                        r.results.forEach((v) => {
                            $("#timer-leaderboard").append(`<div style="margin: 5px 20px 5px 20px; width: 95%; box-sizing: border-box;" class="row justify-center align-center"><div class="small-profile-picture"><img src="https://cdn.nathcat.net/pfps/${v.pfpPath}"></div><h2 style="margin-left: 20px;">${v.fullName}</h2><span class="spacer" style="margin: 0 5px 0 5px; border: 1px solid var(--tertiary-color);"></span><h2>${Math.floor(v.value / 60) + ":" + ((v.value % 60) < 10 ? ("0" + (v.value % 60)) : (v.value % 60))}</h2></div>`);
                        });

                        if (r.results.length === 0) {
                            $("#timer-leaderboard").append("<p><i>No data!</i></p>");
                        }
                    }
                    else {
                        console.error(r.message);
                        $("#timer-leaderboard").append("<p><i>Failed to get leaderboard!</i></p>");
                    }
                });

                fetch("https://data.nathcat.net/data/get-leaderboard-state.php", {
                    method: "POST",
                    body: JSON.stringify({
                        "leaderboardId": 3,
                        "orderBy": "DESC",
                        "limit": 5
                    })
                }).then((r) => r.json()).then((r) => {
                    if (r.status === "success") {
                        r.results.forEach((v) => {
                            $("#solved-leaderboard").append(`<div style="margin: 5px 20px 5px 20px; width: 95%; box-sizing: border-box;" class="row justify-center align-center"><div class="small-profile-picture"><img src="https://cdn.nathcat.net/pfps/${v.pfpPath}"></div><h2 style="margin-left: 20px;">${v.fullName}</h2><span class="spacer" style="margin: 0 5px 0 5px; border: 1px solid var(--tertiary-color);"></span><h2>${v.value}</h2></div>`);
                        });

                        if (r.results.length === 0) {
                            $("#solved-leaderboard").append("<p><i>No data!</i></p>");
                        }
                    }
                    else {
                        console.error(r.message);
                        $("#solved-leaderboard").append("<p><i>Failed to get leaderboard!</i></p>");
                    }
                });
            </script>

            <?php include("footer.php"); ?>
        </div>
    </body>
</html>