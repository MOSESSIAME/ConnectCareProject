<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MembersExport implements FromQuery, WithMapping, WithHeadings
{
    public function __construct(private Builder $query) {}

    public function query()
    {
        return $this->query->with(['serviceUnit','homecell'])
            ->select([
                'members.id','members.full_name','members.type','members.phone','members.email',
                'members.address','members.service_unit_id','members.homecell_id',
                'members.foundation_class_completed','members.created_at',
            ]);
    }

    public function headings(): array
    {
        return ['ID','Name','Type','Phone','Email','Address','Service Unit','Homecell','Foundation Class','Created'];
    }

    public function map($m): array
    {
        return [
            $m->id,
            $m->full_name,
            $m->type,
            $m->phone,
            $m->email,
            $m->address ?? 'N/A',
            optional($m->serviceUnit)->name ?? 'N/A',
            optional($m->homecell)->name ?? 'N/A',
            $m->foundation_class_completed ? 'Completed' : 'Pending',
            optional($m->created_at)?->format('Y-m-d'),
        ];
    }
}
