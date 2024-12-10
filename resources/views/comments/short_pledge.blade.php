### {{ $intro }}
**{{ $donorName }} pledged ${{ $amount }}, making the total bounty ${{ $totalBounty }}! :tada: View the issue on [OpenPledge](<{{ $issueLink }}>) :computer:** 

@if($expireDate)
${\textsf{\color{red}🚨 Time's ticking! This pledge expires on {{ $expireDate }} 🚨}}$
@endif