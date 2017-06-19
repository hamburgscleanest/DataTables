CREATE VIRTUAL TABLE testmodels USING fts4(id, `name`, created_at);

INSERT INTO testmodels
VALUES (1, 'test-text', '2000-02-02'),
  (2, 'lorem ipsum', '2000-02-02'),
  (3, 'datatables', '2000-02-02'),
  (4, 'fulltext', '2000-02-02'),
  (5, 'more text', '2000-02-02'),
  (6, 'testing stuff', '2000-02-02'),
  (7, 'just text', '2000-02-02'),
  (8, 'hello world', '2000-02-02');