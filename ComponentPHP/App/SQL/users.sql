!@( component|get_user )
SELECT * FROM Test WHERE user = !@( $user )
!@( end )

!@( component|get_all_users )
SELECT * FROM Test
!@( end )

!@( component|add_user )
INSERT INTO Test (user) VALUES (!@( $user ))
!@( end )
