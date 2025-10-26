
### 💰✨ Another Pledge! ✨💰

| **Pledger** | **Amount Pledged** |
|-------|----------------|
| {{ $donorName }} | {{ $amount }}$ |
| TOTAL SUM IS NOW | {{ $totalBounty }}$ |

@if($expireDate)
    ${\textsf{\color{red}🚨 Time's ticking! This pledge expires on {{ $expireDate }} 🚨}}$
@endif

Pledges are growing! 📈
View this issue on OpenPledge and [claim the pledge now.](<{{ $issueLink }}>)
