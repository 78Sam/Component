!@(component|get_login_user_by_username)
SELECT *
FROM Users
WHERE username = !@($username);
!@(end)

!@(component|get_user_by_username)
SELECT Users.id, Users.username, Users.joined, Users.role
FROM Users
WHERE username = !@($username);
!@(end)

!@(component|get_user_by_id)
SELECT Users.id, Users.username, Users.joined, Users.role
FROM Users
WHERE id = !@($id);
!@(end)

!@(component|create_user)
INSERT INTO Users (username, password, joined, role) VALUES (!@($username), !@($password), !@($joined), !@($role));
!@(end)
