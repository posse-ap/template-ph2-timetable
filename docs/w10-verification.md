# W10 検証メモ

## 管理者権限

- 学生が `GET /admin/entries` を開くと 403。
- 学生が `POST /admin/entries/{entry}/approve` を直接送っても 403。
- `W10EnrollmentApprovalTest::test_non_admin_cannot_open_or_post_to_admin_approval_routes` で確認。

## 状態遷移

- 却下済みの申請を承認しようとすると、状態は変わらず「申請中の授業だけ承認できます。」。
- 承認済みの申請を学生が取り消そうとすると、状態は変わらず「申請中の授業だけ取り消せます。」。
- 却下済みの授業に再申請しようとすると、「この授業はすでに申請済み、または履修履歴があります。」。
- `W10EnrollmentApprovalTest::test_only_pending_entries_can_change_state` で確認。

## 同時承認

ゼミ相当の一時授業（定員3人）に承認済み2人・申請中2人を作り、2処理を並行実行した。

- 授業行のロックなし: 承認済み4人（定員超過）。
- `courses` の対象行を `FOR UPDATE` でロック: 承認済み3人。
- 検証用の授業・ユーザー・申請は確認後に削除済み。

実装では授業行を `lockForUpdate()` してから承認済み人数を数えるため、同じ授業の承認処理が直列化される。

## Seeder

`W10InitialDataSeeder` を2回実行して確認した。

- `admin@example.com` の管理者は1人。
- `SE300` の定員は3人。

## psql

状態別件数:

```text
approved | 5
```

定員超過を1本のSQLで確認:

```sql
SELECT
    c.code,
    COUNT(te.id) FILTER (WHERE te.status = 'approved') AS approved_count,
    c.capacity
FROM courses c
LEFT JOIN timetable_entries te ON te.course_id = c.id
GROUP BY c.id, c.code, c.capacity
HAVING COUNT(te.id) FILTER (WHERE te.status = 'approved') > c.capacity
ORDER BY c.code;
```

結果は0件。

## AIレビューの判断

- 採用: 承認・却下時に `decided_at` が保存されることをテストする。テストで未保存を検出したため、`TimetableEntry::$fillable` に追加した。
- 採用: 管理者画面のディレクトリ名 `adimin` を `admin` に修正した。コントローラーのビュー名と一致させるため。
- 採用: 承認時は申請行だけでなく授業行をロックする。同じ授業の別申請を同時処理しても定員判定を直列化するため。
- 採用: 受講者一覧は `approved` の申請だけを表示する。申請中・却下済みを受講者として数えないため。
