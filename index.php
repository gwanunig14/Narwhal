<html>
<head>
    <title>PHP Test</title>
    <link rel="stylesheet" href="css/main.css">
</head>
<body>
    <?php
    function get_track_data() {
        $url = 'https://itunes.apple.com/lookup?id=74752665,730091,826980,149376,994656,154011,633206,132659,3296287,271498616,889780,147559,364554,1855044,129477464,3447642,315448610,486597,63748,267981,486493,155097,166529,575609,60960&entity=song&limit=1';

        $rawtracks=file_get_contents($url);
        $tracks = json_decode($rawtracks, true)['results'];

        $all_tracks = array();
        foreach ($tracks as $all_track_data) {
            if ( $all_track_data['kind'] === 'song' ) {
                $track_data = array();
                $track_data['image_data'] = $all_track_data['artworkUrl100'];;
                $track_data['track_name'] = $all_track_data['trackCensoredName'];
                $track_data['track_url'] = $all_track_data['trackViewUrl'];
                $track_data['artist_name'] = $all_track_data['artistName'];
                $track_data['artist_url'] = $all_track_data['artistViewUrl'];
                $track_data['album_name'] = $all_track_data['collectionName'];
                $track_data['album_url'] = $all_track_data['collectionViewUrl'];

                array_push($all_tracks, $track_data);
            }
        }

        return $all_tracks;
    }

    function sort_array($tracks, $sort_direction, $sort_by, $sort_string) {
        $titles = array_column($tracks, $sort_by);

        array_multisort($titles, $sort_direction, $tracks);

        display_tracks($tracks, $sort_string);
    }

    function filter_array($tracks, $post) {
        if(array_key_exists('album_abc', $post) || array_key_exists('album_zyx', $post)) {
            $sort = 'album_name';
        } else if(array_key_exists('artist_abc', $post) || array_key_exists('artist_zyx', $post)) {
            $sort = 'artist_name';
        } else {
            $sort = 'track_name';
        }

        $filtered_tracks = array();
        foreach ($tracks as $track) {
            if (strpos(strtolower($track[$sort]), $post['filter']) !== false) {
                array_push($filtered_tracks, $track);
            }
        }
        
        return $filtered_tracks;
    }

    function display_tracks($tracks, $sort_string) {
        ?><p>
            <?=$sort_string?>
        </p>
        <?php
        foreach ($tracks as $track) {
            ?>
            <div>
                <a href=<?=$track['album_url']?>>
                    <img src=<?=$track['image_data']?>/>
                </a>
                <a href=<?=$track['track_url']?>>
                    <p><?= $track['track_name'] ?></p>
                </a>
                <a href=<?=$track['artist_url']?>>
                    <p><?= $track['artist_name'] ?></p>
                </a>
            </div>
            <br>
        <?}
    }

    $tracks = get_track_data();

    if ($_POST['filter'] && $_POST['filter'] !== '/') {
        $filter = $_POST['filter'];
    } else {
        $filter = '';
    }
    

    ?>
    <form method="post">
        <input type="submit" name="track_abc"
               class="button" value="Sort by song (abc)" />

        <input type="submit" name="track_zyx"
               class="button" value="Sort by song (zyx)" />

        <input type="submit" name="artist_abc"
               class="button" value="Sort by artist (abc)" />

        <input type="submit" name="artist_zyx"
               class="button" value="Sort by artist (zyx)" />

        <input type="submit" name="album_abc"
               class="button" value="Sort by album (abc)" />

        <input type="submit" name="album_zyx"
               class="button" value="Sort by album (zyx)" />
        
        <input type="text" name="filter"
               id="filter" value=<?=$filter?> />
    </form>

    <div id=columns>
        <?php
        $filtered_tracks = $tracks;

        if($_POST['filter']) {
            $filtered_tracks = filter_array($filtered_tracks, $_POST);
        }

        if(array_key_exists('track_zyx', $_POST)) {
            sort_array($filtered_tracks, SORT_DESC, 'track_name', 'Song Title (ZYX)');
        } else if(array_key_exists('album_abc', $_POST)) {
            sort_array($filtered_tracks, SORT_ASC, 'album_name', 'Album Title (ABC)');
        } else if(array_key_exists('album_zyx', $_POST)) {
            sort_array($filtered_tracks, SORT_DESC, 'album_name', 'Album Title (ZYX)');
        } else if(array_key_exists('artist_abc', $_POST)) {
            sort_array($filtered_tracks, SORT_ASC, 'artist_name', 'Artist Name (ABC)');
        } else if(array_key_exists('artist_zyx', $_POST)) {
            sort_array($filtered_tracks, SORT_DESC, 'artist_name', 'Artist Name (ZYX)');
        } else {
            sort_array($filtered_tracks, SORT_ASC, 'track_name', 'Song Title (ABC)');
        }
        ?>
    </div>

</body>
</html>