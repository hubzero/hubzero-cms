DROP FUNCTION IF EXISTS count_in_list;
DELIMITER ~~
-- Count occurrences of id in list, where list is something like '1,2,2,3,4'
CREATE FUNCTION count_in_list(id int, list text) RETURNS integer
BEGIN
  DECLARE rv int;
  DECLARE pos int;
  DECLARE off int;
  DECLARE search_val text;
  SET rv = 0;
  SET off = 1;
  SET search_val = concat(',', id, ',');
  SET list = concat(',', list, ',');
  LOOP
    SET pos = INSTR(MID(list, off), search_val);
    IF pos = 0 THEN
      RETURN rv;
    END IF;
    SET rv = rv + 1;
    SET off = off + pos;
  END LOOP;
END ~~
DELIMITER ;
