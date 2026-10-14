-- W1 POSSE課題：index.html の各区画と同じ結果を返す SELECT 文を書く
-- 書いた SQL は教材サイトの「SQL練習場」で実行し、表示が index.html の区画と一致することを確かめてから書くこと
-- 曜日（day）は数字のまま返してよい

-- 区画1：月曜日の授業（時限の早い順） POSSE 
 SELECT period AS 時限, name AS 授業名, teacher AS 担当教員, room AS 教室
FROM courses
WHERE day = 1
ORDER BY period;

-- 区画2：必修科目（科目コードの順）
SELECT name AS 授業名, credits AS 単位数
FROM courses
WHERE required = 1
ORDER BY code;


-- 区画3：3限以降の2単位の授業（曜日→時限の順）
SELECT
day,period,name
FROM courses
WHERE period >= 3 AND credits = 2
ORDER BY day, period;


-- 区画4：演習の授業（科目コードの順）

SELECT name AS 授業名, teacher AS 担当教員, room AS 教室
FROM courses
WHERE name LIKE '%演習%'
ORDER BY code;
-- 区画5：J棟の授業（金曜以外。曜日→時限の順）

SELECT
  CASE day
    WHEN 1 THEN '月' WHEN 2 THEN '火' WHEN 3 THEN '水'
    WHEN 4 THEN '木' WHEN 5 THEN '金'
  END AS 曜日,
  period AS 時限,
  name AS 授業名,
  room AS 教室
FROM courses
WHERE room LIKE 'J%' AND day <> 5
ORDER BY day, period;

-- 区画6：担当教員が未定の授業（科目コードの順）
SELECT code AS 科目コード, name AS 授業名
FROM courses
WHERE teacher IS NULL
ORDER BY code;

