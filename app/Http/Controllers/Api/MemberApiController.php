<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MemberResource;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MemberApiController extends Controller
{
    public function index(Request $request)
    {
        $query = Member::with(['serviceUnit','homecell']);

        if ($term = $request->get('q')) {
            $query->where(function($q) use ($term) {
                $q->where('full_name','like',"%{$term}%")
                  ->orWhere('phone','like',"%{$term}%")
                  ->orWhere('email','like',"%{$term}%");
            });
        }

        $members = $query->orderBy('full_name')->paginate(25);
        return MemberResource::collection($members);
    }

    public function show(Member $member)
    {
        return new MemberResource($member->load(['serviceUnit','homecell']));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'full_name' => 'required|string|max:100',
            'gender'    => ['required', Rule::in(['M','F'])],
            'phone'     => 'nullable|string|max:20',
            'email'     => 'nullable|email|unique:members,email',
            'address'   => 'required|string|max:255',
            'type'      => 'required|in:First-timer,New Convert,Existing Member',
        ]);

        $member = Member::create($data);

        return (new MemberResource($member))->response()->setStatusCode(201);
    }

    public function update(Request $request, Member $member)
    {
        $data = $request->validate([
            'full_name' => 'sometimes|required|string|max:100',
            'gender'    => ['sometimes','required', Rule::in(['M','F'])],
            'phone'     => 'nullable|string|max:20',
            'email'     => ['nullable','email', Rule::unique('members','email')->ignore($member->id)],
            'address'   => 'sometimes|required|string|max:255',
            'type'      => 'sometimes|required|in:First-timer,New Convert,Existing Member',
        ]);

        $member->update($data);
        return new MemberResource($member);
    }

    public function destroy(Member $member)
    {
        $member->delete();
        return response()->json(['message' => 'Member deleted.'], 200);
    }
}
