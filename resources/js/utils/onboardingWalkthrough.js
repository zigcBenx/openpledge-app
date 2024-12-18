import Shepherd from "shepherd.js";
import { router } from "@inertiajs/vue3";

const initiateShepherdTour = () => {
    return new Shepherd.Tour({
        useModalOverlay: true,
        defaultStepOptions: {
            scrollTo: true,
        },
    });
};

const getNextButton = (shepherdTour) => {
    return {
        text: "Next",
        classes: "shepherd-button shepherd-button-primary",
        action: shepherdTour.next,
    };
};

const getExitButton = (shepherdTour, redirectToDiscoverIssues = false) => {
    return {
        text: "End Tour",
        classes: "shepherd-button-text",
        action: () => {
            localStorage.removeItem("isTutorialInProgress");
            shepherdTour.complete();

            if (redirectToDiscoverIssues) {
                router.visit(route("discover.issues"));
            }
        },
    };
};

export const getDiscoverIssuesTour = (githubUser, repository) => {
    const shepherdTour = initiateShepherdTour();
    const nextButton = getNextButton(shepherdTour);
    const exitButton = getExitButton(shepherdTour);

    shepherdTour.addStep({
        title: "Welcome to OpenPledge!",
        text: "Let’s walk through the main features.",
        buttons: [nextButton, exitButton],
    });

    shepherdTour.addStep({
        title: "Explore the Issues Table",
        text: "This table displays all open issues with key details like the issue title, repository, labels, programming language, and the current donation amount. Click on an issue name to view its full details and support it directly!",
        attachTo: { element: "#issues-table", on: "top" },
        buttons: [nextButton, exitButton],
    });

    shepherdTour.addStep({
        title: "Refine Your Search with Filters",
        text: 'Use the Filters panel to narrow down issues by selecting labels, programming languages, pledge range, and creation date. Click "Apply Filters" to update the list or "Clear" to reset all filters.',
        attachTo: { element: "#filter-issues-button", on: "left" },
        buttons: [nextButton, exitButton],
    });

    shepherdTour.addStep({
        title: "Connect Your GitHub Repository",
        text: "To link a repository, simply paste its GitHub URL (e.g., https://github.com/laravel/laravel) into this field and press Enter. You’ll be redirected to the repository page where you can complete the connection to OpenPledge.",
        attachTo: { element: "#repository-connect-container", on: "left" },
        buttons: [nextButton, exitButton],
    });

    shepherdTour.addStep({
        title: "Search Issues and Repositories",
        text: 'Use this search bar to quickly find issues and repositories within OpenPledge. By default, it searches only connected items. Check "Show GitHub results" to broaden your search to all of GitHub for a wider range of issues and repositories.',
        attachTo: { element: "#search-input", on: "bottom" },
        buttons: [nextButton, exitButton],
    });

    shepherdTour.addStep({
        title: "Explore the Repository Overview!",
        // text: "Next, we’ll dive into a repository to explore how OpenPledge enables collaborative open-source contributions in greater detail.",
        text: "You can visit repository page through search bar or by clicking it's name on issue table. Here is the example of already connected repository.",
        buttons: [
            {
                text: "Go to Repository",
                classes: "shepherd-button shepherd-button-primary",
                action: () => {
                    localStorage.setItem("isTutorialInProgress", "true");
                    shepherdTour.complete();
                    router.visit(
                        route("repositories.show", { githubUser, repository })
                    );
                },
            },
            exitButton,
        ],
    });

    return shepherdTour;
};

export const getIssueTour = () => {
    const shepherdTour = initiateShepherdTour();
    const nextButton = getNextButton(shepherdTour);
    const exitButton = getExitButton(shepherdTour, true);

    shepherdTour.addStep({
        title: "Issue Overview",
        text: "Here, you can see detailed information about this issue, including its status, description, and any related activity.",
        buttons: [nextButton, exitButton],
    });

    shepherdTour.addStep({
        title: "Pledge or Solve the Issue",
        text: `In the sidebar, you have two main options:<br>
    
    - <strong>Pledge</strong>: You can pledge an amount to support this issue, with the choice of an infinite pledge or a pledge with an expiry date. If you select the expiry date option, you'll receive a refund if the issue isn’t resolved within the set time.<br>
    
    - <strong>Solve</strong>: If you want to work on resolving the issue, select the "Solve" tab. This will walk you through the steps to fork, branch, and submit your solution to the repository. Selecting "Solve" adds the issue to your active issues list, and you’ll receive updates on its progress.`,
        attachTo: { element: "#issue-sidebar-container", on: "left" },
        buttons: [nextButton, exitButton],
    });

    shepherdTour.addStep({
        title: "Issue Activity",
        text: `This section displays all recent activity related to the issue, keeping you up-to-date on its progress. Here’s what you can expect:<br><br>
    - <strong>Issue Status Changes</strong>: When the issue is opened, closed, or reopened, you’ll see an update here.<br><br>
    - <strong>Pull Requests</strong>: Any pull requests connected or disconnected from this issue are logged here.<br><br>
    - <strong>Comments</strong>: Contributions and feedback from other users, including comments, will be displayed, helping you stay informed on discussions. (Bot comments are not shown.)<br><br>
    - <strong>Pledges</strong>: Any new pledges or support for the issue are tracked here, giving you insight into the community's support.`,
        attachTo: { element: "#issue-activity-container", on: "right" },
        buttons: [nextButton, exitButton],
    });

    shepherdTour.addStep({
        title: "You’re All Set!",
        text: `Thank you for exploring OpenPledge! 🎉 We hope this tour helped you get familiar with the platform and discover new ways to support and resolve open-source issues. If you ever need to revisit the guide, click on profile icon and "Start guided tour" again.<br><br>Click "Finish" to return to the main Issues page and start making an impact!`,
        buttons: [
            {
                text: "Finish",
                classes: "shepherd-button shepherd-button-primary",
                action: () => {
                    localStorage.removeItem("isTutorialInProgress");
                    shepherdTour.complete();
                    router.visit(route("discover.issues"));
                },
            },
        ],
    });

    return shepherdTour;
};

export const getRepositoryTour = (issue) => {
    const shepherdTour = initiateShepherdTour();
    const nextButton = getNextButton(shepherdTour);
    const exitButton = getExitButton(shepherdTour, true);

    shepherdTour.addStep({
        title: "Repository Overview",
        text: `Welcome to the repository overview! Here, you’ll find essential details about this repository as well as issues linked to this repository.`,
        buttons: [nextButton, exitButton],
    });

    shepherdTour.addStep({
        title: "Favorite Repositories and Issues",
        text: `Click the <strong>star icon</strong> to favorite this repository or any issue you find interesting! Favoriting allows you to save repositories and issues for quick access later. You can view all your favorited items in your profile, making it easier to keep track of the projects and issues that matter most to you.`,
        attachTo: { element: "#favorite-icon", on: "top" },
        buttons: [nextButton, exitButton],
    });

    shepherdTour.addStep({
        title: "Filter Issue Types",
        text: `Easily toggle between <strong>Pledged Issues</strong> (those with active donations) and <strong>Open Issues</strong> (all available issues, whether funded or not). This helps you quickly find the issues that best match your interests or goals.`,
        attachTo: { element: "#issue-types-container", on: "right" },
        buttons: [nextButton, exitButton],
    });

    shepherdTour.addStep({
        title: "Follow us to the Issue overview!",
        text: "Now, let’s go into an issue to see how OpenPledge works in more detail.",
        buttons: [
            {
                text: "Go to Issue",
                classes: "shepherd-button shepherd-button-primary",
                action: () => {
                    shepherdTour.complete();
                    router.visit(route("issues.show", { issue }));
                },
            },
            exitButton,
        ],
    });

    return shepherdTour;
};
