!@(component|add_song)
INSERT INTO Songs (title, artist, added_by, added_at, duration, lookup_key) VALUES (
    !@($title),
    !@($artist),
    !@($added_by),
    !@($added_at),
    !@($duration),
    !@($lookup_key)
);
!@(end)

!@(component|get_song_by_title_artist)
SELECT Songs.*, Users.id AS userId
FROM Songs
JOIN Users ON Songs.added_by = Users.id
WHERE title = !@($title) AND artist = !@($artist);
!@(end)
