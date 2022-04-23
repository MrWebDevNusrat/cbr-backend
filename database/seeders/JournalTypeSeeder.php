<?php

namespace Database\Seeders;

use App\Models\Crm\JournalType;
use App\Models\Crm\JournalTypeTranslation;
use Illuminate\Database\Seeder;

class JournalTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $local_journal = JournalType::firstOrCreate([
            'id'=>1,
            'created_by' => auth()->id(),
        ]);

        $local_journal_uz = JournalTypeTranslation::firstOrCreate([
            'journal_type_id' => $local_journal->id,
            'language' => 'uz',
            'name' => 'Mahalliy jurnal',
        ]);


        $local_journal_ru = JournalTypeTranslation::firstOrCreate([
            'journal_type_id' => $local_journal->id,
            'language' => 'ru',
            'name' => 'Местный журнал',
        ]);

        $local_journal_en = JournalTypeTranslation::firstOrCreate([
            'journal_type_id' => $local_journal->id,
            'language' => 'en',
            'name' => 'Local magazine',
        ]);

        $foreign_journal = JournalType::firstOrCreate([
            'id'=>2,
            'created_by' => auth()->id(),
        ]);

        $foreign_journal_uz = JournalTypeTranslation::firstOrCreate([
            'journal_type_id' => $foreign_journal->id,
            'language' => 'uz',
            'name' => 'Xorijiy jurnal',
        ]);


        $foreign_journal_ru = JournalTypeTranslation::firstOrCreate([
            'journal_type_id' => $foreign_journal->id,
            'language' => 'ru',
            'name' => 'Иностранный журнал',
        ]);

        $foreign_journal_en = JournalTypeTranslation::firstOrCreate([
            'journal_type_id' => $foreign_journal->id,
            'language' => 'en',
            'name' => 'Foreign magazine',
        ]);

        $international_journal = JournalType::firstOrCreate([
            'id'=>3,
            'created_by' => auth()->id(),
        ]);

        $international_journal_uz = JournalTypeTranslation::firstOrCreate([
            'journal_type_id' => $international_journal->id,
            'language' => 'uz',
            'name' => 'Xalqaro jurnal',
        ]);

        $international_journal_ru = JournalTypeTranslation::firstOrCreate([
            'journal_type_id' => $international_journal->id,
            'language' => 'ru',
            'name' => 'Международный журнал',
        ]);

        $international_journal_en = JournalTypeTranslation::firstOrCreate([
            'journal_type_id' => $international_journal->id,
            'language' => 'en',
            'name' => 'International journal',
        ]);

        $republic_conference_journal = JournalType::firstOrCreate([
            'id'=>4,
            'created_by' => auth()->id(),
        ]);

        $republic_conference_journal_uz = JournalTypeTranslation::firstOrCreate([
            'journal_type_id' => $republic_conference_journal->id,
            'language' => 'uz',
            'name' => 'Respublika konferensiya jurnali',
        ]);

        $republic_conference_journal_ru = JournalTypeTranslation::firstOrCreate([
            'journal_type_id' => $republic_conference_journal->id,
            'language' => 'ru',
            'name' => 'Республиканский конференц-журнал',
        ]);

        $republic_conference_journal_en = JournalTypeTranslation::firstOrCreate([
            'journal_type_id' => $republic_conference_journal->id,
            'language' => 'en',
            'name' => 'Republican Conference Journal',
        ]);
    }
}
