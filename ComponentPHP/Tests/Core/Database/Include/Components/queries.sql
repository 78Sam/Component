!@( component|select_all )
SELECT * FROM Test;
!@( end )

!@( component|select_all_where )
SELECT * FROM Test WHERE data = !@( $value );
!@( end )
