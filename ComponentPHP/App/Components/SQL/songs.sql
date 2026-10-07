-- ! Get

!@(component|get_song_by_title_artist)
SELECT
    Songs.*,
    Users.id AS user_id,
    Users.username,
    Users.joined,
    Users.role
FROM Songs
JOIN Users ON Songs.added_by = Users.id
WHERE title = !@($title) AND artist = !@($artist);
!@(end)

!@(component|get_all_songs)
SELECT
    Songs.*,
    Users.id AS user_id,
    Users.username,
    Users.joined,
    Users.role
FROM Songs
JOIN Users ON Songs.added_by = Users.id;
!@(end)

!@(component|get_song_by_id)
SELECT
    Songs.*,
    Users.id AS user_id,
    Users.username,
    Users.joined,
    Users.role
FROM Songs
JOIN Users ON Songs.added_by = Users.id
WHERE Songs.id = !@($id);
!@(end)

-- ! Add

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

-- ! Delete

!@(component|delete_song_by_id)
DELETE FROM Songs WHERE id = !@($id);
!@(end)
