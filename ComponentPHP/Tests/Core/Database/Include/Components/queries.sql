!@(component|select_all)
SELECT * FROM Test;
!@(end)

!@(component|select_all_where)
SELECT * FROM Test WHERE data = !@($value);
!@(end)

!@(component|select_all_where_nested)
SELECT * FROM Test WHERE !@($condition);
!@(end) 

!@(component|or_condition)
data = !@($value1) OR data = !@($value2)
!@(end)
