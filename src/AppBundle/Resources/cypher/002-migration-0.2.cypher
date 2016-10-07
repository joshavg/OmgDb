MATCH (a:attribute)
  SET a.datatype = "datatype." + a.datatype;
