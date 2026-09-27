!@(component|get_user_by_username)
SELECT * FROM Users WHERE username = !@($username);
!@(end)

!@(component|create_user)
INSERT INTO Users (username, password) VALUES (!@($username), !@($password));
!@(end)
