<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tag;
use App\Models\Course;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        // タグ名と、そのタグを付ける科目コード
        $data = [
            '演習あり' => [
                'CS101', 'PG220', 'DS230',
                'PG120', 'UX250', 'BZ120'
            ],

            '少人数' => [
                'EN101', 'EN102', 'CN100',
                'UX250', 'SE300'
            ],

            '英語で実施' => [
                'EN101', 'EN102'
            ],

            'レポート評価' => [
                'IE110', 'SO120', 'HI130',
                'LI140', 'CA200', 'ART100'
            ],

            '1年生向け' => [
                'CS101', 'EC100', 'IE110',
                'EN101', 'PS100', 'LW110'
            ],
        ];

        foreach ($data as $name => $codes) {

            // ① 同じ名前のタグがなければ作成
            $tag = Tag::firstOrCreate([
                'name' => $name
            ]);

            // ② 科目コードから授業IDを探す
            $ids = Course::whereIn('code', $codes)
                ->pluck('id')
                ->all();

            // ③ すでにある関連は残し、足りない分だけ追加
            $tag->courses()
                ->syncWithoutDetaching($ids);
        }
    }
}
