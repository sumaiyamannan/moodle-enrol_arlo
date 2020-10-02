Release 13.0 (1st October 2020):
================================

Perform
-------

    Totara 13 adds an entirely new product, Totara Perform, to the Totara codebase.
    This has been implemented as a number of separate components, some of which are
    unique to Perform, some of which are part of the core platform and some of which
    are a mixture. For specific information about what has changed in 13, see the
    What's new page:
    
    https://help.totaralearning.com/display/TH13/What%27s+new

  Performance activities (Perform only)

    Performance activities are a new component that support the creation of and
    participation in appraisals, check-ins, 360 feedbacks and other workflow-based
    form activities. It includes support for creating custom forms with workflows,
    assignment and tracking progress, sending notifications and reporting on
    responses. More information about this feature can be found in the end-user
    documentation:

    https://help.totaralearning.com/display/TH13/Managing+performance+activities

    Technical documentation can be found here:

    https://help.totaralearning.com/display/DEV/Performance+activities+architecture

  Competencies (Partly core platform, partly Perform only)

    The existing competency functionality remains in the core platform and is
    largely unchanged. For Perform customers the functionality has been extended to
    support a range of additional behaviours. Competencies can now be assigned to
    groups of users, so it's possible to track who is required to achieve specific
    competencies. Competency criteria has been extended to support pluggable
    criteria and aggregation, so you can set up complex rules to achieve completion.
    End users now see a competency profile which displays their progress visually,
    and they can drill down to see achievement criteria and an activity log history.
    Competencies support rating by a range of roles.

    More information about this feature can be found in the documentation:

    https://help.totaralearning.com/display/TH13/Competency+assignment

  Evidence (Core platform)

    The core evidence functionality has been extended and improved in a number of
    ways. The management of evidence has been moved to a user's own dedicated
    evidence bank, and there are permissions to allow self-management of evidence or
    management by a user's manager. Evidence types have been improved to support
    different custom fields per type. Evidence created via course or certification
    completion import will continue to show up in the record of learning but other
    evidence is now displayed in the evidence bank only.

    More information about this feature can be found in the documentation:

    https://help.totaralearning.com/display/TH13/Evidence

Engage
------

    Totara Engage is a brand new product in Totara codebase, a Learning Experience
    platform introduced in Totara 13 release. It unlocks the feature set to enable
    user-generated content creation and curation workflows, workspaces creation and
    collaboration, brings recommendations of formal and informal learning and
    integration with Microsoft Teams.

    More information about this  can be found in the documentation:

    https://help.totaralearning.com/display/TH13/What%27s+new

  Library, content creation and curation (Engage only)

    With Totara Engage there's now the ability for all the users to create content
    and curate it for other people. Users get access to their libraries, where they
    get their content and content others shared with them organised. From there they
    can create resources, surveys or playlists and share them with other users. The
    permissions to create content are defined on the level of capabilities. A new
    taxonomy level - topics - was introduced for categorisation of Totara Engage
    content. Learning catalogue was extended to include Resources and Playlists.

    https://help.totaralearning.com/display/TH13/Library

    Technical documentation can be found here:

    https://help.totaralearning.com/display/DEV/Engage+resources

  Workspaces (Engage only)

    After activating Totara Engage, users will be able to create workspaces (groups)
    for collaboration, link formal and informal learning to the workspaces. It
    allows flexible setting of access permissions to the workspaces and reveals
    collaborative tools in the workspaces - discussion thread, workspace files and
    workspace library.

    More information about this feature can be found in the documentation:

    https://help.totaralearning.com/display/TH13/Workspaces

  Recommendations (Engage only)

    With activating Totara Engage users to get access to the Totara-powered
    proprietary recommendations engine, which analyses user's interaction with the
    system, other users' interactions and recognises what the content is about and
    based on these results recommends users formal and informal content in dashboard
    blocks and side panels.

    More information about this feature can be found in the documentation:

    https://help.totaralearning.com/display/TH13/Recommendations

    Technical documentation can be found here

    https://help.totaralearning.com/display/DEV/Recommender+installation+and+configuration

  Microsoft Teams Integration (Engage only)
    
    With Totara Engage users get the plugin that enables integration with Microsoft
    Teams. With this integrations it will be possible to surface Totara content in
    Microsoft teams, push all the notifications there and create content (Resources
    and playlists) and then share it within Microsoft Teams.

    More information about this feature can be found in the documentation:

    https://help.totaralearning.com/display/TH13/Microsoft+Teams+integration+setup

Tui front end framework
-----------------------

    Totara has a new Vue.js based frontend framework that we call Tui. It enables us
    to create rich, modern, and reactive experiences within our product.

    It is separated from the main body of the codebase as discussed above in code
    reorganisation and is distributed under a proprietary licence.

    Tui is a frontend framework, consisting of Vue.js components that embrace modern
    development practices when manipulating the DOM and working with data. Tui
    requires a build process during development that takes the Vue component files,
    which include JavaScript, SCSS, HTML and Language strings, and compiles them
    into bundles for functionality and style. These compiled files are referred to
    as build files.

    A new server plugin called totara_tui facilitates the use of Tui on pages by
    outputting a minimal block of HTML markup that will be replaced by Tui pages and
    experiences when the page loads on the client. The totara_tui plugin also
    mediates the compiled build files when requested by the client.

    Entire pages and experiences are created using Tui.

    Tui then communicates with the server via WebAPI and more specifically using
    GraphQL queries and mutations.

    Importantly, and as noted in the backwards compatibility section, Tui is opt-in
    for developers. While the new products (Totara Engage and Totara Perform) use it
    extensively, if you are working with existing plugins you can continue to do
    exactly what you did before. As a bonus in that situation you can additionally
    tap into the WebAPI GraphQL queries and mutations if you choose, from your
    plugin, without needing to use Tui.

    For more information on Vue.js we recommend you read the [official Vue.js
    documentation](https://vuejs.org/). Our implementation of Vue.js within Tui
    follows their guidelines.

    In addition to this we use the [Apollo client](https://apollographql.com/client)
    and [Vue Apollo|https://apollo.vuejs.org/] libraries in order to extend Vue.js,
    and to enable easy integration and communication between Tui and Totara core via
    the WebAPI GraphQL services.

    For detailed information on the architecture, principles and how to's of Tui
    please see our [Tui front end
    framework](https://help.totaralearning.com/display/DEV/Tui+front+end+framework)
    developer documentation.

New features
------------

  TL-18501 Cloud-based file storage support

    Totara can now make native use of S3 and Azure cloud storage for data directory
    storage.

    This is an advanced configuration option that will enable those using these
    engines to optimise data directory storage within Totara.
    These changes are also compatible with xsendfile (X-Accel-Redirect) and through
    dual configuration with web servers such as nginx enable serving files directly
    from disk bypassing php passthrough.

  TL-18605 GraphQL-based Web API

    Totara 13 has a brand-new service layer we call Web API. At it's heart Web API
    uses the GraphQL query language. This enables rich descriptions of the APIs and
    makes a range of developer tools immediately available, making it easier to work
    with the new service layer.
    Perform, Engage, and Mobile all use this new service layer extensively.

    The rich description includes not just the queries and mutations used to get
    information from the system, and manipulate it, but also provides a description
    of the data that enables the explanation of object types, fields, interfaces,
    and others.

    The Web API implementation of GraphQL follows the official specification that
    can be found at: https://graphql.org|https://graphql.org/. For those interested,
    we recommend first learning about GraphQL from the official site.

    The following are the notable decisions made when in our Web API GraphQL
    implementation:
    * Each component and plugin can introduce its own schema; at runtime these
      schema files from all components and plugins are built into a complete schema
      file for the site that is cached and mediated by Totara.
    * We only support named queries (sometimes referred to as persistent queries).
      This means you can only call pre-defined queries and mutations. You cannot write
      you own query on the client and execute it.
    * Queries can be batched, in order to support the bulk loading of information.
    * Each consumer should have its own endpoint, and can have its own named queries
      and mutations. Totara Core ships with two such endpoints, the AJAX endpoint that
      can be used from a browser, and a Mobile endpoint used by our new mobile app.
    * The server-side implementation of the resolvers marries up with the intended
      flexibility of the query language – each individual type and field is
      independently resolvable.
    * We use an open-source PHP GraphQL library as the base of our implementation.
      Read more at https://github.com/webonyx/graphql-php.
    * Query and mutation resolvers can use middleware as a way to include reusable
      code to be called before or after a resolver is called. See
      https://help.totaralearning.com/display/DEV/Middleware for technical
      documentation of this feature.
    * Entity buffer - this allows you to defer loading of entities by buffering them
      and combining them into a single query across all type resolvers involved in the
      query. It also works across batched queries thus reducing the number of queries
      for the same type drastically. See
      https://help.totaralearning.com/display/Dev/GraphQL for more documentation.

    You can find more on [developing with Web API and GraphQL in Totara
    Core](https://help.totaralearning.com/display/DEV/Integration+with+Totara+Core)
    in our developer documentation.

  TL-18786 Chart.js charts for report builder

    Chart.js has been integrated with Totara. The first use of this library is
    within report builder where it is now used as the default chart library. The
    original charting library SVGGraphs can be reverted to if desired. Information
    on configuring the display of report builder charts can be found in our
    [developer
    documentation](https://help.totaralearning.com/display/DEV/Advanced+settings).

  TL-20345 ORM framework

    Totara now includes an ORM framework based on top of the earlier-introduced
    query builder that further abstracts interaction with the database. Each table
    can be represented by its own entity class which allows for auto-completion of
    the fields, as well as some other convenience methods. It also makes it possible
    to define relations between entities and fetch related data together.

    Please refer to the full documentation available here:
    https://help.totaralearning.com/x/TKbgB

  TL-21039 Added totara_mobile plugin to provide a GraphQL endpoint and other services to the Totara Mobile app

    This new plugin implements the settings and services required by the Totara
    Mobile app, including:
    * App settings such as branding and authentication preference
    * Handshake negotiation between the app and the server
    * Login and device registration
    * A set of dedicated GraphQL queries tailored to the needs of the app
    * Support for app-specific webviews of Totara pages

    This plugin must be enabled in order for users to use the app with your site.
    For more information,
    see https://help.totaralearning.com/display/TM/Introducing+Totara+Mobile

  TL-21164 Multitenancy functionality

    Multitenancy within Totara 13 brings the ability to separate, and if desired,
    isolate users and content, based upon the tenant that a user is a member of.
    It is an advanced feature and must be enabled for the site if wanted. Once
    enabled the tenant management interfaces become available, as does an
    experimental setting to isolate tenants.

    An explanation of what this means in product can be found in our help
    documentation on [using
    multitenancy](https://help.totaralearning.com/display/TH13/Using+multi-tenancy).

    The following are the most notable technical changes to the platform made to
    support this functionality:
    * Tenant context and it's effect on the user context
      With this change we have introduced a new context level CONTEXT_TENANT that must
      have the system context as it's parent. One is created for each tenant.
      A user is either a system user, or a tenant member. If they are a tenant member
      their context level will use the tenant context as its parent. Contexts for
      system users continue to use the system context as a parent.
    * Tenant category
      Each tenant has a tenant category that is created when the tenant is created.
      The tenant category is always a top-level category. It cannot be edited like
      other categories, and cannot be manually deleted.
      Content within the category is considered to belong to the tenant. This includes
      subcategories, courses, activities, and blocks.
    * Permission resolution
      The behaviour of has_capability and associated functions has changed in order to
      support tenants and each context now tracks the tenant that it belongs to. When
      multitenancy is enabled, a tenant member cannot pass capability checks if they
      are made against a context belonging to a different tenant. In other words, a
      member of one tenant cannot access content or users belonging to a different
      tenant.

    A tenant member can still access system content and users (contexts that do not
    belong to a tenant) providing isolation mode has not been turned on. When
    isolation mode has been turned on, tenant members can only access content and
    users within their tenant.

  TL-21193 Added Laravel-like Query Builder

    This patch introduces a query builder which abstracts querying the database on
    top of the DML layer. The query builder is inspired by [Laravel’s Query
    Builder](https://laravel.com/docs/master/queries) and provides a similar
    feature set. It provides a consistent fluent interface. Internally it uses the
    DML layer to execute queries so database compatibility and low-level query
    integrity is ensured. The query builder provides at least the same functionality
    as the DML layer. It should be possible to substitute existing DML actions with
    it, as well as cover more complex cases which are only possible via raw SQL
    queries at the moment.

    Full documentation is available here:
    https://help.totaralearning.com/display/DEV/Query+builder

  TL-22816 Added support for migration from Moodle 3.5.10, 3.6.8, 3.7.4 and 3.8.1

  TL-23805 AirNotifier push notification message output plugin

    An AirNotifier push notification message output plugin has been added to Totara
    to provide support for push notifications being sent from Totara through to the
    Totara Mobile App.

  TL-24352 Implemented an MVC framework

    The totara_mvc plugin enables developers to implement new pages by using
    controllers and view, which can reduce boiler plate code. It comes with a
    generic controller class and an admin controller class which can be extended and
    supports views (generic, report and tui view classes are included in
    totara_mvc).

    Technical documentation can be found here:

    https://help.totaralearning.com/display/DEV/Model-view-controller+%28MVC%29+pattern

  TL-27902 Totara containers

    Totara 13 introduces a new conceptual approach to how course core functionality
    works. To allow for more flexibility around different use cases and
    requirements, we abstracted out core functionality that separates the courses
    core API from course implementation itself. Now course in its conventional
    meaning is a specific implementation of container.

    This functionality provides an easier way to introduce and maintain various
    types of containers that require the advantages of course API without having to
    create a generic course. This is done by setting up specific hooks across course
    API codebase that allow redefining how any feature, plugin or sub-plugin used in
    courses works, as well as regulate its availability and appearance.

    This API is still backwards compatible. However, many legacy functions were
    deprecated. Legacy courses and related plugins behaviour have not been changed.

    For more information about container feature and the way it works, please visit
    https://help.totaralearning.com/display/DEV/Totara+containers

  TL-27903 Weka editor

    Totara 13 has a brand-new Weka editor that edits a structured document
    representation stored as JSON, instead of directly editing HTML. Weka is built
    on top of the ProseMirror rich text editor framework (https://prosemirror.net/).

    This enables users to produce content that can be rendered in multiple ways,
    such as HTML, plain text, and markdown. Furthermore, the content can be
    supported by different platforms rather than just web-based, e.g: Mobile apps.

    The JSON document produced by Weka editor is built out of smaller structured
    nodes, nested according to a predefined schema. Here are notable things about
    nodes:
    * Each component and plugin can introduce its own node(s), and it will be
      automatically picked up by the editor.
    * Each component and plugin can decide whether the node is available for the
      certain area or not.
    * Each node has a limited set of valid attributes with defined meanings. This
      allows renderers to be built for the format across multiple output formats.

    Apart from the ability to extend the nodes of the JSON document, we also enabled
    the ability to let plugins configure the Weka editor in different areas.

    For more information, please
    visit https://help.totaralearning.com/display/DEV/Weka+editor

Security issues
---------------

  TL-20656 Improved server-side validation of audience rules

    Server-side code handling audience forms has been reviewed to ensure that all
    incoming data is correctly validated against the expected format for the rule
    being created/edited.

  TL-20704 Improved the format_string() function to prevent XSS when results are not properly encoded in HTML attributes

    Previously it was possible to enable the use of arbitrary HTML tags in course
    and activity names. This is a security risk and is no longer allowed.

  TL-20729 All text is now consistently sanitised before being displayed or edited

    Prior to this change, privileged users could introduce security vulnerabilities
    through areas such as course summaries, section descriptions and activity
    introductions.

    The original purpose of the functionality was to allow content creators to use
    advanced HTML functionality such as iframes, JavaScript and objects. In some
    areas it was explicitly allowed to happen. In others, the trusttext system was
    used to manage who could embed potentially harmful content.

    This patch includes the following changes:
    * A new setting 'Disable consistent cleaning' has been introduced. It is set to
      'off' by default.
    * Text in the affected areas will be now be sanitised, both when it is
      displayed, and when it is loaded into an editor.
    * The trusttext system will be forced off by default and be disabled unless the
      new setting is turned on.
    * SVG images will be served with more appropriate content-disposition headers.

    The consequence of this change is that by default no user will be able to use
    the likes of iframes, JavaScript or object tags in the majority of places where
    they previously could.

    For those who rely on the old behaviour, the new 'Disable consistent cleaning'
    setting can be enabled in order to return to the old behaviour. However we
    strongly recommend that you leave this setting off, as when it is turned on the
    security vulnerabilities will be present. When enabled, this setting will be
    shown in the security report.

    Please be aware that there is a data-loss risk for any sites which are upgrading
    to this release and have relied upon the previous behaviour if they have not
    enabled the new 'Disable consistent cleaning' setting. After upgrading, unless
    you enable the legacy behaviour, when a user edits content relying upon this
    functionality and saves it, they will cause the cleaned version to be saved to
    the database. Any unallowed HTML tags, or attributes, will have been removed.

    For more information on this change, and a list of affected areas, please refer
    to our help documentation.
    https://help.totaralearning.com/display/DEV/Totara+13+changes+to+content+sanitisation

  TL-20891 Ensured user identity fields are consistently sanitised

  TL-20996 Ensured user email addresses are consistently sanitised

Performance improvements
------------------------

  TL-19815 Improved performance of replace_all_text() method in the DML layer

    This improved performance of unsupported 'DB Search and replace' tool. Instead
    of blind attempts to search and replace content in all rows, it selects only
    rows that have searched content first.
    Contributed by Jo Jones at Kineo UK

  TL-21853 Improved the performance of the course and category management interface

    The contents of each category's 'Actions' cog menu on the 'Course and category
    management' page is now rendered upon request. This provides a noticeable
    performance improvement over rendering them all in advance.

  TL-27549 Improved instance-creation performance

Improvements
------------

  TL-5081  Added a new 'Program exceptions' report source

    Contributed by Mark Ward at Learning Pool

  TL-5287  Added additional options to seminar notification recipients field

    Previously, the selection of seminar notification recipients was limited to a
    few classes of attendee: booked, wait-listed, cancelled, and request pending. It
    was possible to refine the booked category to be either 'all events', attended,
    or no-show. There was no way to target attendees on just current or future
    events.

    The recipient selection interface has been upgraded to allow independent
    selection of attendees who are booked on future, current, or past events; and
    also attendees who are fully attended, partially attended, unable to attend, or
    no-show. The options for wait-listed, cancelled, and pending requests remain
    unchanged.

  TL-5629  Added temporary manager and expiry date to HR Import Job Assignment element

    As part of this the pre-existing Job Assignment import field
    'managerjobassignmentidnumber' has been renamed to 'managerjaidnumber'.

    Important: CSV or database sources that do not have field mappings will require
    the source field name to be updated.

  TL-5660  Uploading completion records no longer creates evidence for unrecognised records by default

    Previously, when uploading course or certification completion data using a CSV
    file, an evidence record would be created for any row in the file that did not
    match up exactly with an existing course or certification. The default was to
    create generic evidence, but other 'Default evidence types' were selectable.

    The new default 'Default evidence type' setting is 'Do not create evidence'.
    This will cause unmatched rows to be marked as errors instead of being used to
    create evidence records.

    To recreate the old behaviour, set 'Create generic evidence' as the 'Default
    evidence type' for the import.

  TL-6204  Customisable font when exporting a report to PDF

    A new setting has been introduced that allows the font used within report
    builder PDF exports to be customised. This allows those on non-standard
    installations to work around required fonts that they do not have.

  TL-6693  Added audience rules for position and organisation multi-select custom fields

    Previously you could create audience rules based on other position and
    organisation custom fields (menu of choices, checkboxes etc), but not based on
    multi-select custom fields. This patch adds a new rule type for multi-select
    custom fields that has 4 operators
    * in all of the selected options
    * in any of the selected options
    * not in all of the selected options
    * not in any of the selected options

    It is worth noting that the in any/all operators will include users that have at
    least one job assignment that have all/any of the selections. Similarly the not
    in any/all operators will include users that have at least one job assignment
    that does not have all/any of the selections. None of the operators will include
    users with no job assignments.

  TL-6695  Added new course or program assignment dynamic audience rule

    This new rule allows you to include or exclude users from an audience based on
    their enrolment in specified courses or programs.

  TL-6725  Expanded the 'Has direct reports' audience rule options

    Previously there were two options:
    * Has direct reports
    * Does not have direct reports

    There are now four options available.
    * 'None': the user has no direct reports
    * 'At least': the user has exactly X reports, or more
    * 'No more than': the user has less than or exactly X reports
    * 'Exactly': the user has exactly X reports

    During upgrade:
    * Any rules previously using 'Has direct reports' will be converted to use 'At
      least' and '1' user.
    * Any rules previously using 'Does not have direct reports' will be converted to
      use 'None'.

    This will ensure that behaviour does not change when upgrading.

  TL-7394  Added a new dynamic audience rule based on historic course completion dates

    This new rule closely resembles the existing course completion rules, but
    instead of comparing the user's current completion it checks the rule against
    any archived completions in the course_completion_history table.
    Contributed by Jamie Kramer at Elearning Experts

  TL-7808  Added seminar reset functionality to course reset

    Previously, seminars did not have any code supporting course reset
    functionality.

    Now if you attempt to reset a course containing a seminar activity there are
    options to 'Delete attendees' and 'Delete all events'. Both are ticked by the
    'Select default' button, but can be unticked to keep events, or keep events and
    their attendees, after the course is reset.

  TL-7967  Changed the certification workflow to only reset primary certification path on expiry

    Previously, when a recertification window opened, courses on both primary
    certification and recertification paths were reset. Now only the recertification
    path courses will be reset when the recertification window opens. Primary
    certification path courses will now be reset only on expiry, and only if they
    are not also on the recertification path. This ensures that courses are only
    reset when they need to be recompleted, and that progress towards
    recertification, if applicable, will contribute to primary certification in the
    event of a user's certification expiring.

  TL-8300  Added the ability to order courses within a Program or Certification course set

    Courses within Program and Certification course sets can now be ordered as
    desired. This order is then reflected when displaying the list of courses back
    to the end user.
    Contributed by Chris Wharton at Catalyst EU

  TL-8308  Improved aggregation support for the certification report source

    Previously the certification report source contained several required columns in
    order to ensure user visibility was correctly applied. These required columns
    led to aggregation within the report not working. Thanks to improvements made in
    Totara 12 this could be refactored so that the required columns were no longer
    required. Visibility is still calculated accurately and aggregation is now
    working for this report source.

  TL-8314  Improved aggregation support for the program report source

    Previously the program report source contained several required columns in order
    to ensure user visibility was correctly applied. These required columns led to
    aggregation within the report not working. Thanks to improvements made in Totara
    12 this could be refactored so that the required columns are no longer
    necessary. Visibility is still calculated accurately and aggregation is now
    working for this report source.

  TL-8315  Improved aggregation support for the course report source

    Previously the course report source contained several required columns in order
    to ensure user visibility was correctly applied. These required columns led to
    aggregation within the report not working. Thanks to improvements made in Totara
    12 this could be refactored so that the required columns are no longer
    necessary. Visibility is still calculated accurately and aggregation is now
    working for this report source.

    Please note that the course report source no longer supports caching.

  TL-8754  Added a 'Has temporary reports' dynamic audience rule

    This rule allows you to add users who are currently serving as a temporary
    manager for one or more users to a dynamic audience.

  TL-9209  Added a new dynamic audience rule based on user creation dates

    This rule allows you to define an audience based on the 'timecreated' column of
    a user's database record. Like existing date time rules, this can either be
    compared to an entered date/time, or to the current time when the rule is
    reaggregated.

  TL-11158 Changed the duration format to use a calendar day if a session event crosses midnight

    The duration is now displayed as a calendar day if it is a multi-day session.
    For example, if a session starts at 1:00pm 30/01/2019 and finishes at 11:00am
    31/01/2019, then the duration displays "2 days" instead of "22 hours".

  TL-12692 Added the ability to track attendance at the session level of seminars

    Previously it was only possible to track attendance at the event level of a
    seminar. With this improvement, attendance can be tracked for each individual
    session within an event. This includes:

    * A new seminar setting, 'Session attendance tracking', which allows trainers to
      record attendance for each session of a seminar event. The recorded session
      attendance is summarised on the event attendance form, allowing trainers to use
      it as the basis for setting an overall attendance status for each attendee.
    * A new seminar setting, 'Mark attendance at', which determines when trainers
      are allowed to begin taking attendance for an event or session.
    * A new attendance status, 'Unable to attend', which provides an option for
      trainers to mark an attendee as not having attended a session or event, but
      without marking them as a 'no show'. The 'Restrict subsequent sign-ups to'
      setting now includes 'Unable to attend' as one of its options.
    * The seminar events dashboard has been consolidated into a single list of
      sessions and events, with a filter allowing participants to see all events, or
      only those that are upcoming, in progress, or in the past. 
    * If 'Session attendance tracking' is enabled, a per-session 'Attendance
      tracking' column appears on the events dashboard, allowing trainers to see at a
      glance which sessions are marked or are ready to be marked.

  TL-14764 Added support for Open Badges Specification 2.0 and Open Badges 2.0 platforms

  TL-15758 Added a 'require passing grade' conditional access criteria to the assignment module

  TL-17209 Converted the seminar wait-list page into an embedded report

  TL-17469 Added dynamic audience rule for 'Has indirect reports'

    Created a dynamic audience rule based on whether the person has indirect
    reports.

  TL-17778 Added image optimisation to Totara catalogue and Featured Links block

    Images used within the catalogue and within the Featured Links blocks are now
    optimised for their use in these locations prior to delivery.

  TL-17930 Enable a report builder saved search to be used as the default view for the report

    As a report builder report curator, a saved search can be set as the report
    default view. This search will be applied as a default view for everyone who has
    visibility of the report. Viewers of the report can remove the default or change
    to another saved search so that they have their own saved view.

  TL-18678 Replaced the course selector form element used for 'Recurring course' when editing program content with the standard course selector form element

    Prior to this change, all courses were loaded into a single dropdown, which
    could lead to performance issues on sites with a large number of courses. This
    dropdown has now been replaced with the standard course selector dialogue
    already used in selecting courses for program course sets.

  TL-19259 Added a 'Has appraisees' dynamic audience rule

  TL-19447 Added 'Totara grid catalogue' option to 'Default home page for users' setting

    Totara grid catalogue can now be selected as the default home page for users. At
    the same time the default value for the 'Allow default page selection' setting
    has been changed from 'Yes' to 'No'.

  TL-19493 A link to the component overview screen is now shown when viewing Learning Plan component items

    A link has been added to the screen for individual Learning Plan component items
    (e.g., a specific course, program, competency, or objective) that returns the
    user to the component overview screen (e.g., all courses, programs,
    competencies, objectives).

  TL-19799 Removed the non-functional Google Fusion export option

  TL-19808 Allowed CSV import of seminar attendees from files without columns for custom fields

    Seminar attendees can now be imported from CSV files that only have columns for
    required custom fields or, if there are no required custom fields, from a list
    of users with no other columns.

  TL-20041 Added a new setting in the course defaults page to enable/disable the course end date by default when creating a new course

  TL-20051 Added a new Job Assignment ID number dynamic audience rule

    This new rule allows you to include or exclude users from an audience based on
    the idnumber field in their job assignments.

  TL-20248 Filters now only show on the seminar events page if there is content to filter

  TL-20274 Introduced minimum required proficiency setting for competency scales

    Competency scales now have a value that is considered the minimum a user must
    achieve to be considered proficient. Values are no longer individually set as
    proficient or not proficient, but instead will respect this setting on the
    scale.

    This will be set for existing scales automatically on upgrade.

    IMPORTANT: Upgrade will be blocked if the proficient values in the scale are not
    ordered correctly (where there are non-proficient values that are higher on the
    scale than proficient ones). If that is the case, sites can be taken back to a
    release that contains TL-21175 where the proficient setting on individual scale
    values can be modified in order to fix this.

  TL-20397 Redesigned the user reports page

    The user report page was redesigned replacing the list of reports with a
    grid-like user interface showing report thumbnails that reflect the nature of
    the report:
    * table reports
    * graphical reports (with different thumbnails for each chart type)

    Added a button to the page to create new reports that will be shown to users
    with appropriate permissions.

  TL-20400 Changed the default seminar grading method, and added manual grading option to seminar events

    There is a new 'Grading method' setting for seminars, which determines which
    grade to use for the overall activity grade when a learner attends multiple
    seminar events. Choices are 'Highest event grade,' 'Lowest event grade', 'First
    event grade', and 'Last event grade'.

    The default seminar grading method has been changed to 'Highest event grade'.
    Prior to this change, a seminar attendee's grade was based on the last
    attendance taken. The old behaviour can be replicated in practice by setting the
    grading method to 'Last event grade'.

    Trainers now also have the ability to assign arbitrary grades to seminar
    attendees. When 'Event manual grading' is enabled, a 'Grade' column is added to
    the event 'Take attendance' form. For each learner, trainers can set attendance,
    a grade, or both.

  TL-20418 Allow a seminar attendance export in CSV format that can then be imported to update attendance

    Following on the ability to upload seminar attendance in the last release, it is
    now possible to download a seminar attendance report that is already correctly
    formatted for upload.

    Trainers can use the new 'CSV export for upload' to mark event attendance, and
    optionally grade if manual event grading is enabled, in bulk. The file can then
    be uploaded with no further changes to column layout or header names.

  TL-20421 Seminar event attendance and grades can now be imported via CSV

    With this feature, accessible from the seminar event 'Take attendance' page,
    trainers are able to upload a CSV file with attendance information for each
    event attendee. If event manual grading is enabled, the CSV file may also
    include grades.

  TL-20422 Moved seminar event and session details to its own page when managing a seminar event

    Previously details of a seminar event and its associated sessions, including
    room and asset information, were shown to trainers at the top of each seminar
    management tab ('Attendees', 'Cancellations', 'Take attendance', et cetera).
    This information was the same from tab to tab, and pushed unique information and
    functionality down the page.

    Seminar event and session information has now been moved to its own tab, 'Event
    details', and removed from all other seminar management tabs. 

  TL-20423 Replaced all seminar 'Go back' links with 'View all events' buttons

    In order to simplify seminar management and improve usability for trainers, the
    'Go back' links at the bottom of all seminar management screens have been
    replaced with buttons that read 'View all events'.

  TL-20425 Updated seminar event dashboard and course view

    This patch contains several improvements to the seminar event dashboard and the
    course activity view, including:
    * Added 'Previous events time period' options to be able to display only past
      events in the specific time period
    * Redesigned the filter bar with tool-tips and icon
    * Added new filters: booking status, attendance tracking status
    * Reverted the change in TL-19928 (February 2019 release); the seminar event
      dashboard is now back to two tables: one is for upcoming or ongoing events, the
      other is for past or cancelled events
    * Redesigned session list table
    * Rearranged table columns
    * Broke down event status into three types: event time, event booking status,
      and user booking status

  TL-20427 Improved the display and usability of download controls when viewing seminar attendees sign-in sheets

  TL-20441 Converted the seminar cancellation page into an embedded report

  TL-20476 Created new seminar setting 'Passing grade' and added 'Require passing grade' seminar activity completion option

    Seminar activity completion options have been enhanced to bring seminar in line
    with other Totara activities like assignment and quiz. Previously, seminar only
    had a 'Learner must receive a grade to complete this activity' option. This has
    been replaced by a 'Require grade' option with two choices: 'Yes, any grade' and
    'Yes, passing grade'.

    If 'Yes, passing grade' is chosen, a passing grade must be set for the seminar.
    The default passing grade can be set globally. Setting the passing grade higher
    than 0 enables the use of pass/fail marks on the activity completion report.

    In order to provide backward compatibility with previous seminar activity
    completion options, the upgrade will set 'Require grade' to 'Yes, any grade' and
    'Passing grade' to '0' on any seminar where 'Learner must receive a grade to
    complete this activity' is enabled. This has the effect of exactly reproducing
    the previous behaviour.

    In addition, this patch has fixed two other minor issues:
    * the facetoface_signups_status.createdby database field was not being updated
      when taking attendance
    * archived sign-up data entries were not being excluded from the computation of
      a seminar grade

  TL-20546 Added a new 'Event grade' column to seminar signup report source

  TL-20579 Improved deletion confirmation for hierarchy frameworks and items

    This patch unifies deletion confirmation for hierarchy frameworks and items, as
    well as adding details about related data to be deleted in the framework
    confirmation and bulk delete confirmation dialogues.

  TL-20756 Added new custom setting in section links block for the display style of topic link

    The new custom setting in section links block will allow the course editor to
    change the display style of topics within this block. By default, it will
    display the section link as a number. However, the course editor is able to
    switch to either section 'title only' or 'number and title'.
    Contributed by Russell England at Kineo USA

  TL-20799 Added support for whitelisting of known trusted SCORM packages

  TL-21036 Implemented a 'CSV for spreadsheets' export format for report builder

    This new CSV export format is designed for use with spreadsheets.

    It produces a CSV file that is close to RFC4180 but has an escape character in
    front of any data that may be interpreted by the spreadsheet application.

    We recommend that users use this export format if they have to export to CSV but
    intend to open the .csv file in a spreadsheet application as it protects them
    against CSV injection attacks.

    This export format is not enabled by default. Those wanting to use it must
    enable it within the 'Export options' setting for report builder.

  TL-21084 Improved seminar session Date/Time format and export for report builder

    New date columns added:
    * Session Start Date + Excel/ODS export
    * Session Finish Date + Excel/ODS export
    * Session Finish Date/Time (linked to activity) + Excel/ODS export

    Improved:
    * Session Start Date/Time added Excel/ODS export
    * Session Finish Date/Time added Excel/ODS export
    * Session Start Date/Time (linked to activity) added Excel/ODS export
    * Session Start Time added Excel/ODS export
    * Session Finish Time added Excel/ODS export
    * There is a new format for date/time with timezone for report builder:
      '2 July 2019, 5 PM
      Timezone: Pacific/Auckland'
    * All Date/Time columns have a proper ODS/Excel export

  TL-21098 Implemented job assignment GraphQL services and converted the profile page

    This is a technical improvement, introducing new GraphQL services for job
    assignments and converting the profile interface list of jobs to use the new
    services.

    The following types have been added:
    * core_user
    * totara_job_assignment
    * totara_hierarchy_position
    * totara_hierarchy_position_type
    * totara_hierarchy_position_framework
    * totara_hierarchy_organisation
    * totara_hierarchy_organisation_type
    * totara_hierarchy_organisation_framework

    The following queries have been introduced:
    * totara_job_my_assignments
    * totara_job_assignments
    * totara_job_assignment

    The following mutations have been introduced:
    * totara_job_move_assignment
    * totara_job_sort_assignments
    * totara_job_delete_assignment
    * totara_job_create_assignment

  TL-21109 Updated the course and certification completion import tools to use the new evidence functionality

    The course and certification completion import tools have been changed to use a
    system-defined evidence type when importing unrecognised courses and
    certifications. It is now no longer possible to upload custom field data for
    evidence using these tools.

  TL-21115 Added new database settings for encryption of database communication

    Full details on how to configure SSL communication with your database can be
    found in config-dist.php after upgrade.

  TL-21197 SQLSRV SSL connections now support the 'TrustServerCertificate' option

    TL-21115 introduced the ability to force database connections over SSL. However,
    SQLSRV required a signed certificate and there was no way to force the
    TrustServerCertificate connection option through Totara.

    A new dboption 'trustservercertificate' has been added that is passed through to
    the 'TrustServerCertificate' option during connection.

  TL-21198 Added Asset/Room/Facilitator filters to Seminar Sessions report

  TL-21422 Added a setting to display a seminar description on a course's homepage

  TL-21486 Added an 'Edit event' button to the seminar event details tab

  TL-21487 Added ability to mark seminar event and session attendance at different times

    The previous 'Mark attendance at' option is now separated into two options - an
    option as to when you can mark Session Attendance AND a separate option for when
    you can mark Event Attendance.

  TL-21491 Added [seminarname] and [seminardescription] placeholders for Seminar notifications

    The [seminarname] placeholder has been added to replace the [facetofacename]
    placeholder, although the system will still support both [seminarname] and
    [facetofacename] placeholders. An optional placeholder, [seminardescription],
    has also been added.

  TL-21507 New room/asset capabilities given to the 'editingteacher' and 'teacher' roles by default

    The following capabilities are now granted to the 'editingteacher' and 'teacher'
    roles by default:
    * mod/facetoface:manageadhocroom
    * mod/facetoface:managesitewideroom
    * mod/facetoface:manageadhocasset
    * mod/facetoface:managesitewideasset
    * mod/facetoface:manageadhocfacilitator
    * mod/facetoface:managesitewidefacilitator

  TL-21513 Added 'Require event over for' seminar activity completion criteria

    By default, seminar activities are considered complete as soon as the required
    completion criteria are achieved. With the ability to take attendance and mark
    grades at the beginning of sessions (or anytime) it is possible for trainers to
    trigger seminar activity completion before a seminar event is complete. 

    As this may not be a desirable outcome, there is now a 'Require event over for'
    criteria that delays activity completion for 0 or more days after the end of an
    event. To enable this functionality, there is a new scheduled task which is set
    to run every 5 minutes by default. Trigger delayed seminar activity completions
    \mod_facetoface\task\activity_completion_task

  TL-21516 Seminar event sessions can now have facilitators associated with them

  TL-21518 Seminar event dashboard improvements

    The following updates have been made to the seminar event dashboard:
    * Added support to multiple rooms and facilitators
    * Reorganised table column orders
    * Added ability to hide empty table columns
    * Action links and icons have been moved to the new dropdown menu
    * The event list tables are now updated in-place, rather than on page reloading

  TL-21554 Refined access control for privileged users suspending, activating and unlocking user accounts

    There is a new capability 'moodle/user:managelogin' controlling who can suspend,
    activate and unlock user accounts and change their passwords. Previously this
    was controlled via the 'moodle/user:update' capability.

  TL-21559 Added tenancy support to program and certification assignments

    Individual assignments and audience assignments are now restricted by tenancy in
    the selector dialogues.

  TL-21590 Tenant information can now be included when importing users via HR Import

    Added tenant 'tenantmember' and 'tenantparticipant'  to the HR Import user
    source

  TL-21595 Added backend support for report templates in report builder

  TL-21600 Improved the grid items functionality when reducing the browser size

    Previously theme root grid items used by the grid catalogue had white space on
    the right which prevented the full width being used. The issue has now been
    fixed with a new implementation of grid items.

    This will require CSS to be regenerated for themes that use LESS inheritance.

  TL-21686 Added inline editing of report titles when viewing a report

  TL-21739 Added option to display seminar room building and address values in addition to room name

  TL-21744 Added tenancy support to seminar user selector

  TL-21745 Ensured it is not possible to add non-participants as attendees in tenant contexts via CSV

  TL-21909 Added a 'Room name' column to the 'My future bookings' seminar report

    This change only affects new installations; admins of existing sites are
    encouraged to add a 'Room Name (linked to room details page)' column to the 'My
    Future Bookings' report to provide more complete information for learners.

  TL-22047 Renamed 'Hide in My Reports' setting name to 'Hide on user reports list' and updated its help string in report builder

  TL-22049 Moved and renamed 'View This Report' link when editing a report builder report

  TL-22132 Added 'Labels' and 'Summary' fields to report builder sources

    Report builder source classes now have 'sourcelabel' and 'sourcesummary'
    properties which will be used in the new report creation workflow.

  TL-22288 Added additional information to the audience deletion confirmation page

    The confirmation page now better informs the user on how assignments,
    visibility, and access provided to audience members will be affected by the
    deletion.

  TL-22292 Added event attendance status to seminar event reports and the seminar event dashboard

  TL-22362 All role-related changes now trigger events and are logged

    New role-related events:
    * core\event\role_created
    * core\event\role_updated
    * core\event\role_capability_updated (replaces
      core\event\role_capabilities_updated)
    * core\event\role_contextlevel_updated

  TL-22406 Redesigned report builder report creation

    This change improved the workflow for creating reports in report builder. Now
    report creation starts at the new report library that includes all available
    report sources plus the new report templates. The report sources and templates
    are categorised and can be filtered by the target audience in the report
    library.

    The report library is compatible with custom report sources and custom report
    templates so the list of items can be extended by partners.

  TL-22436 Added a new option for requiring users to apply filters before reports can be displayed

  TL-22446 Added already-selected items to seminar resource selection dialogues

    When selecting rooms, facilitators, or assets for a seminar session, the
    selection dialogue now includes any already-selected items, for reference and to
    allow them to be deselected when necessary.

  TL-22470 Added seminar events to facilitator user calendars where appropriate

    In addition, when seminar events are rendered for the calendar, only the session
    that pertains to the calendar date is included.

  TL-22527 Added additional JavaScript polyfills for IE 11

    Added the following polyfills to support modern JavaScript APIs in IE 11:
    * {String, Array}.prototype.includes
    * Array.prototype.\{find, findIndex}
    * NodeList.prototype.forEach
    * Object.\{entries, values}
    * Number.\{isFinite, isInteger, isNaN, parseFloat, parseInt}

  TL-22591 Improved the accessibility of 'Create' menu in the grid catalogue

  TL-22798 Improved the workflow for adding attendees to a seminar

    The process for adding one or more attendees to a seminar involves three steps.
    Step 1 is selecting or providing the list of potential attendees. Step 2 is
    confirming the list and setting the notification preferences. Step 3 creates the
    signups and lists any errors that occurred. 

    This improvement addresses some quirks in the behaviour of this process, and
    makes error handling more consistent. Specifically:
    1.  Learners who are not enrolled in the course are enrolled by the system
    before signup is attempted. Previously, if such a learner could not be signed up
    for any reason, they would remain enrolled in the course. This issue has been
    fixed, and these enrolments are removed at step 3.
    2.  Some checks for whether a learner could sign up to a seminar happened at
    step 1, and others at step 2. All checks now happen at step 2. Learners who can
    sign up are added as attendees (or potential attendees), and learners who cannot
    are listed in the 'Bulk add attendees' error report at step 3.
    3.  Any learners specified at step 1 who could not be matched with users in the
    system would cause the process to fail at step 1. The process now continues for
    learners who are matched with user accounts, and the missing learners are listed
    in the 'Bulk add attendees' error report at step 3.
    4.  Specifying any learners who were already signed up would cause the process
    to fail at step 1. Learners who are already signed up are now silently ignored,
    and the process continues for everyone else.

    Please note this is a change in behaviour: there are no longer any errors that
    stop the bulk add attendees process. Learners who can be signed up are signed
    up, and those who cannot are detailed in the 'Bulk add attendees' error report.

  TL-22972 Added the ability to hide the 'Export attendance' button on the seminar events dashboard

    In order to allow administrators to prevent trainers from downloading the full
    list of seminar attendees, a new 'exportattendance' capability was created.
    Users without this capability do not see the 'Export attendance' button on the
    seminar events dashboard. 

    The new capability has been assigned to all roles that currently have
    'viewattendees' capability by default.

  TL-22993 Simplified default seminar terms and conditions

    The new default text is: 'Check the box to confirm your eligibility to sign up
    to this seminar.'

  TL-22997 Grid catalogue can now be customised with top and bottom page blocks

  TL-23027 Updated who can see 'Join now' links for seminars

    When a room with a room link is assigned to a seminar session, a 'Join now' link
    appears for attendees while the session is in progress. With this patch, the
    'Join now' link also appears for any facilitators assigned to the same session,
    and to any users who are assigned the 'mod/facetoface:joinanyvirtualroom'
    capability in the course or activity context. Trainers and editing trainers are
    given this capability by default.

  TL-23053 Improved user assignment search for appraisals

    Previously, the user search for appraisal assignments, 360 feedback and report
    builder global restrictions did not find expected results for some search terms,
    e.g. when typing first name and last name of a user. This has been improved by
    switching to a better keyword parsing method.

  TL-23411 Prevented potential problems with memory use of record_exists database queries

  TL-23488 Password with one extra trailing space is now considered valid when logging in or changing passwords

  TL-23502 Removed obsolete drivers from auth_db and enrol_database settings

  TL-23820 Added site administration overview link to the site administration menu at the top of the page

  TL-23904 Minor visual improvements to the report selection page within report builder

  TL-23954 Deprecated the Roots and Basis themes

  TL-24308 Added the ability to have guest accessible dashboards

    There is a new setting for dashboards that enables guest access to a dashboard.
    Prior to this change guests were not able to view any dashboards.

    When enabling this setting be aware that it may expose sensitive content on the
    dashboard to guests.

  TL-24326 Deprecated the 'Flash animation' player plugin

  TL-24327 Deprecated the 'Email protection' content filter

  TL-24328 Deprecated the 'Demo' course format plugin

  TL-24329 Deprecated the 'Social' course format plugin

  TL-24334 Removed deprecated 'mod_assignment' and 'tool_assignmentupgrade' plugins

  TL-24335 Deprecated the 'Survey' activity module

  TL-24337 Deprecated the 'Mentees' block

  TL-24338 Removed the already-deprecated 'Quiz results' block

  TL-24339 Migrated profile fields with obsolete messaging IDs to hidden custom profile fields

    The affected fields are 'ICQ number', 'Yahoo ID', 'MSN ID' and 'AIM ID'.

  TL-24340 Deprecated the 'HTML tidy' content filter

  TL-24387 Improved seminar notification view by adding link to resource title

  TL-24399 Converted monolithic user profile to multiple instances of new 'User Profile block'

    The automatically-generated comprehensive user profile has been removed and
    replaced with a block area to allow full customisation of the user profile page.

    Additionally, the ability to create custom user profiles has been removed.
    Custom user profiles will persist until the admin clicks the 'Reset profile for
    all users' button on the default user profile. At that point, only the default
    user profile will remain, and all users will always see the default. Users will
    not have the option of customising their profile page by adding blocks.

    A new block, 'User Profile', has been created which will display selected items
    from a single user profile category. By using multiple User Profile blocks, one
    for each category, an admin can define exactly which categories and fields
    should be displayed on user profiles.

    A limited number of user profile blocks have been added to the default profile
    to recreate the essence of the old user profile and ensure that key
    functionality is still accessible to users. These can, of course, be
    reconfigured or removed as necessary.

  TL-24432 Removed the already-deprecated 'Course progress report' block and the 'Frontpage combolist' block

  TL-24459 Improve keyboard accessibility of the InlineEditing component

  TL-24475 All MNET functionality has been deprecated and will be removed in Totara 14

  TL-24476 Deprecated the 'Legacy log' log store plugin

  TL-24509 Removed support for gopher and ftp links from HTML texts due to lack of support in modern browsers

  TL-24554 Added a tooltip to 'Claim URL' field when creating badges

  TL-24660 Added a new 'Certification Exceptions' report source

  TL-24664 Implemented a flexible means for plugins to introduce preview options for image handling

    Prior to this patch, the options for query string 'preview' in image url are
    limited to three options.

    With this patch, the new options are able to be added via other plugins and can
    be overridden by theme.

  TL-24684 Removed references to the deprecated Mozilla badge backpack

  TL-24703 Added a new learning plan assignment type for programs

    Previously, when a program was added to a user's learning plan, it was not
    considered as 'Required learning'. This has now changed. All programs added to a
    user's learning plan are now considered as 'Required learning'.

    The learning plan assignment behaviour is the same as the other assignment
    types except that there is no user interface for the learning plan
    assignments within the program assignments section. Learning plan assignments
    can only be made from the learning plan interfaces and not from within the
    program itself. The creation of learning plan assignments only occurs when the
    learning plan is approved. When a program is removed from a learning plan, and
    the changes are approved, learning plan assignments to the program will be
    removed.

    When upgrading to Totara 13 from earlier versions, learning plan assignments
    will be created as required.

  TL-24759 Added a setting that allows MS Teams to access catalogue images

    A new setting 'Force users to log in to view catalogue pictures' has been added
    to Totara. When enabled it ensures that users are required to log in in order to
    access catalogue entry images. The setting is on by default.

    When turned off, users attempting to access these images will not be required to
    log in. This enables MS Teams and other integration to also access these images.

    We recommend keeping this setting turned on unless public access to these images
    is explicitly required for your site.

  TL-24910 Improved file serving performance through the use of read-only lock-less session mode

  TL-24975 Deprecated contextual user profile page

  TL-25020 Added new 'details content' setting to the grid catalogue general settings

    If this setting is enabled, as it will be automatically for any upgrading sites,
    there should be no change in current behaviour. When a catalogue item is
    clicked, a details pop-up will be displayed with a view or enrol button.

    If the setting is disabled, as it will be automatically for any new
    installations, when you click an item on the catalogue it will redirect you
    immediately to the URL used by the view button in the details pop-up instead of
    displaying the details.

  TL-25164 Implemented generic relationships for resolving relationships between users

  TL-25276 Added various improvements in Redis session locking and debugging

  TL-25321 Added a new native MySQL locking factory

  TL-25334 Added a new native MSSQL Server locking factory

  TL-25382 Added a new native Redis session handler

    See sample '\core\session\redis5' session handler configuration in
    config-dist.php for more information.

  TL-25463 Added support for Open Badges 2.1

  TL-25719 Added the usability of the job assignments form by grouping based upon purpose

  TL-25727 Added support for Redis Sentinel in Redis MCU store and session handler

  TL-25758 The sessions looper only returns the facilitating sessions on facilitator notifications

  TL-25760 Added the following seminar placeholders

     
    * [coursenamelink] - Name of course with link
    * [seminarnamelink] - Name of seminar activity with link
    * [eventpagelink] - Link to the event page

  TL-26100 Added support for 'customhelptext' option to filters

    Provides 'customtext' option for filters so the help text can be customised.

  TL-26107 Added EXIF orientation correction to preview image generation

    Provided the PHP EXIF extension is available, Totara will use the EXIF
    orientation tag to correct rotated and/or mirrored JPEG images taken by a
    smartphone.

  TL-26130 Removed the ability to create/manage a custom user profile

    Existing custom user profiles are not deleted, but new ones cannot be created.
    The default user profile is used for all users.

  TL-26198 Disabled SCORM network connectivity checks when using mobile app webview

  TL-26408 Updated rules for showing course statuses when viewing certifications

    This change removes the display of irrelevant progress bars when viewing a
    certification. When a user is on the original certification path, progress for
    courses on the re-certification path are not shown, and when the user is on the
    re-certification path the original certification path course progresses are not
    shown. This is because course progress gets reset for the user when they are
    transitioned into the next path.

  TL-26606 Added ability to set Totara form action_button to be a primary button

    This adds the 'primary-btn' class to the button and it is styled accordingly.

  TL-26704 Added support for read replicas within the Redis cachestore

  TL-26928 Replaced bespoke user details header in competencies with system-wide component

  TL-26963 Reorganised the advanced feature's admin settings pages

  TL-27001 Changed course section summary format of new sections to the preferred editor format

    Previously, when sections were added to a course, the format of the section
    summary was hard-coded to HTML. The course creator's editor preference setting
    is now used to determine what format should be used for the summary of new
    sections.

  TL-27243 Fixed dropdown group keyboard focus

    Prevented group names in dropdowns from receiving focus when navigating with a
    keyboard.

  TL-27274 Changing a user's password now redirects back to the user's preference page

  TL-27545 Added support for bulk inserts in the Mssql database driver

  TL-27580 Ensured third party libraries are all correctly recorded in product

  TL-27656 Added Perform feature usage to registration data

    Added high-level usage data on Perform features into the registration data
    system.

    This patch adds high-level counts of number of records from certain database
    tables, for the purpose of assessing general system usage:
    *  Performance activities
    ** Performance activities enabled
    ** Number of activities
    ** Number of user assignments
    ** Number of subject instances
    ** Number of participant instances
    ** Number of element responses
    * Competencies
    ** Competency assignments enabled
    ** Number of user assignments
    ** Number of assignments
    ** Number of achievements
    * Evidence
    ** Evidence enabled
    ** Number of evidence items
    ** Number of evidence types

  TL-27676 Added Engage feature usage to registration data

    Added high-level usage data on Engage features into the registration data
    system.

    This patch adds high-level counts of number of records from certain database
    tables, for the purpose of assessing general system usage:
    * Workspaces
    ** Workspaces enabled
    ** Number of workspaces
    ** Number of workspace discussions
    * Recommender
    ** Recommender enabled
    ** Number of interactions
    ** Number of items
    ** Number of trending items
    ** Number of users
    *  Comments
    ** Number of comments in system
    * Engage
    ** Resources enabled
    ** Number of surveys
    ** Number of resources
    ** Number of bookmarks
    ** Number of ratings
    * MS Teams
    ** MS Teams enabled
    ** Number of bots
    ** Number of MS team users
    ** Number of channels
    ** Number of subscriptions
    ** Number of MS team tenants
    * Playlists
    ** Number of playlists
    * Reactions
    ** Number of reactions
    * Topics
    ** Number of topics
    ** Number of topic instances

  TL-27795 Moved shared CSS variables from Vue components to variable files

    SCSS variables declared within a Vue component shouldn't be used within other
    components. Common variables should instead be declared within SCSS. This patch
    moves all currently shared variables into SCSS.

  TL-27806 Added core feature usage to registration data

    Added high-level usage data on core features into the registration data system.

    This patch adds high-level counts of number of records from certain database
    tables, for the purpose of assessing general system usage:
    * Catalogue
    ** Catalogue mode (grid, report, etc)
    ** Active learning types (course, program, certification, etc)
    * Mobile
    ** Mobile enabled
    ** AirNotifier enabled
    ** AirNotifier customised (true if no longer using the default URL)
    ** Number of devices that have been linked
    ** Number of offline SCORMs
    ** Number of mobile compatible courses

  TL-27834 Added single column layout component for Vue pages

    Added a single column layout component for Vue pages. This allows for the
    structure of Vue pages to use a more consistent structure.

  TL-27872 Enabled site admin to request an auto-generated AirNotifier app code from push.totaralearning.com

  TL-27888 Improved database version detection for MySQL and MariaDB

  TL-27912 Changed default database collation for MySQL 8 to utf8mb4_0900_as_cs

    Please note that MariaDB does not have any suitable case and accent sensitive
    collation – production servers should be upgraded to MySQL 8.

  TL-27934 Added caching to get_site() function

    Adds a new cache definition for storing the site course in an application cache
    for faster retrieval of the site course on each request.

Bug fixes
---------

  TL-14099 Fixed a bug in course completion determination when multiple enrolments are present

    Previously when a user has multiple enrolments in the same course, and course
    completion is determined by how many days the user was enrolled in the course,
    the cron job that updated course completions would fail.

  TL-16324 Fixed global search navigation when Solr is enabled and configured

    Prior to this patch, the 'Manage global search' page would only be shown in the
    site administration structure on certain pages. It is now shown consistently
    when intended.

  TL-18946 Added missing recipient types and descriptions to seminar notifications

    Prior to this patch, there were a few notifications in seminar that did not
    specify the recipient types nor the description of the notification.

    With this patch, the recipient types and description of notifications are now
    specified.

  TL-19054 Set notification type when cloning a report builder embedded report to a warning instead of an error

  TL-20305 Prevented filters from being changed on the seminar events dashboard while events are loading

  TL-20327 Fixed race condition when dialogs are not initialised when adding components to a learning plan

  TL-20338 Removed deleted users from seminar views

    Prior to this patch, when a user record was deleted from the system, all of the
    user's signup records remained visible in seminar views.

    With this patch, only users with permission to see deleted users
    (totara/core:seedeletedusers capability) will be able to see or modify the
    signups of deleted users.

  TL-20453 Fixed broken 'Turn editing off' link on the seminar attendees page

  TL-20513 Ensured that seminar activity 'View all events' link on course homepage isn't hidden by horizontal scrollbar on Mac OS

    On Mac OS, the default System Preference is to hide scrollbars until needed.
    When the scrollbars are shown, they may obscure content or make it difficult to
    click links that are underneath them. This was sometimes the case with the 'View
    all events' link under seminar activities on course homepages.

    The link has been made larger, and padding added, to ensure that it is still
    clickable if a horizontal scrollbar appears under it.

  TL-20520 Fixed saved-search functionality on seminar room and asset embedded reports

    Added 'rb_config' and '$sid' to asset and room embedded reports to ensure saved
    searched can be viewed.

  TL-20547 Fixed JavaScript validation on Moodle forms

    Previously, when calls were made to $PAGE->get_end_code(false), AMD JavaScript
    was not being added to the HTML. This has now been corrected. This enables
    Moodle form validation when editing appraisals, audience rules and seminar
    times, rooms and assets. 

  TL-20629 Fixed sign-up links on course page that pointed to the wrong URL when seminar direct enrolment was enabled

  TL-20685 Fixed a bug preventing the export of seminar events

  TL-20793 Fixed handling of the 'required' attribute when applied to the Atto editor

  TL-20804 Seminar 'Add users' step 2 now respects the 'showuseridentity' config setting

    Previously, user full name, email address,  username and ID number were
    displayed in step 2 of the 'Add user' workflow without respecting the
    'showuseridentity' config setting. Now ID number and username are no longer
    shown, and display of email address respects the 'showuseridentity' config
    setting.

  TL-20847 Fixed a bug that prevented taking seminar session attendance in some cases

    In the previous release of Totara Evergreen, when the in-memory list of seminar
    sessions was sorted, it did not maintain an ID-to-session relationship. This
    caused seminar session attendance to fail with an error because the requested
    session could not be looked up by ID.

    With this patch, session IDs in the list are preserved during sorting, allowing
    the requested session to be found.

  TL-20854 Fixed the creation and editing of multi-select cohort rules

    TL-20547 introduced a regression when editing a multi-select cohort rule where
    it couldn't be saved. This is now fixed.

  TL-20987 Fixed double encoding of user identity fields in the history grader report

    Any customisations made using the '/grade/report/history/users_ajax.php' file
    should check the output of user identity fields after upgrade to ensure proper
    sanitisation is happening on output.

  TL-20998 Fixed possible double entity encoding when rendering templates in javascript

    This was evident in default column names when creating new reports in report
    builder, but has been fixed in core template to resolve any unfound instances.

  TL-20999 Fixed seminar grade input field to respect the course 'grade_decimalpoints' configuration

  TL-21001 Fixed regression in the report builder management UI where special characters were incorrectly encoded as entities

  TL-21049 Fixed improperly removed seminar event roles

    Seminar refactoring in the previous release created a bug that led to improper
    deletion of seminar event roles. This, in turn, caused an error when attempting
    to update seminar events that had unassigned event roles.

    The bug has been fixed, and improperly deleted roles will be removed correctly
    on upgrade.

  TL-21117 Fixed a bug that generated the wrong page URL for seminar session 'Take attendance' page

  TL-21149 Images displayed in a static form field no longer cause horizontal scroll

    This will require CSS to be regenerated for themes that use LESS inheritance.

  TL-21252 Added database table keys skipped during upgrade and migration

  TL-21275 Fixed recent regression with double encoded entities in report exports

    Replaced relevant report builder calls to 'format_string()' with calls to the
    report builder display class 'format_string' which correctly encodes the string
    according to the output format.

  TL-21290 Fixed report builder saved searches to be sorted alphabetically in the 'Manage your saved searches' dialogue 

  TL-21365 Removed duplicate records from the cancelled attendees list for seminar events with multiple sessions

  TL-21378 Updated seminar 'Message users' tab to respect 'User identity' settings when displaying lists of users

  TL-21412 Fixed database query logging when ignoring errors in the database transactions

  TL-21436 Updated seminar date/time columns in report builder to use the correct timezone

    Seminar sessions can be set to display their start and end time in a particular
    timezone, known as the event timezone. Aside from the start and end time, all
    other seminar date/time values (such as the signup period start and end time, or
    the date and time when a user declares interest) use the system timezone.

    This update causes all seminar-related date/time values, except for the session
    start and end times, to be displayed using the system timezone.

  TL-21536 Updated the default capabilities of the Trainer and Editing Trainer roles to allow 'mod/facetoface:viewallsessions'

    Previously the Trainer and Editing Trainer roles were unable to view the seminar
    'Event details' page without the 'mod/facetoface:viewallsessions' capability.
    These roles will now have the capability enabled by default for new
    installations. Sites upgrading to this release are recommended to manually
    enable the capability for the roles.

  TL-21631 Fixed inconsistent booking status in events and sessions report

    Previously events with booking status 'closed' were showing as open in the
    events and session reports. Now the 'booking status' column is updated in both
    reports to reflect the actual booking state.

  TL-21992 Fixed incorrect graph layouts when using the progress chart type in report builder

  TL-22001 Fixed minor visual bugs in ChartJS pie and doughnut charts

    Several small visual improvements have been made to the pie and doughnut chart
    types within ChartJS:

    * Chart colours have been adjusted to ensure similar hues no longer appear next
      to each other in a chart.
    * Thin white borders have been added between slices.
    * Increased the inner edge diameter for doughnut charts, reducing their
      thickness.

  TL-22041 Ensured activity descriptions are consistently cleaned

    Prior to this change, the activity descriptions on the course page were cleaned
    regardless of the 'Disable consistent cleaning' setting. This was inconsistent
    with the display of activity descriptions throughout the rest of the site.

    The 'Disable consistent cleaning' setting is now consistently respected.

  TL-22062 Allow more relevant access to goal names in the 'Goal status' report

    Previously, the 'Goal name' column had been changed to only appear when the user
    had the 'totara/hierarchy:viewallgoals' capability due to data privacy and
    protection concerns for user reports created by the report source. This resulted
    in user's being unable to see their own company goals in the embedded or user
    reports. Similarly, managers could not see their team's goals in the report
    unless they had this broad capability.

    Now, the 'Goal name' column is shown (using a new report builder display class)
    if the viewer has any of the following:
    * the capability 'totara/hierarchy:viewallgoals'
    * the goal is their own and they have the 'totara/hierarchy:viewownpersonalgoal'
      capability
    * the goal belongs to someone the viewer manages and they have
      'totara/hierarchy:viewstaffpersonalgoal' capability.

  TL-22124 Fixed line chart line colour not matching the dots in ChartJS

  TL-22239 Added a missing 'Number of unable to attend' column in seminar report

  TL-22241 Fixed the description of date-based dynamic audience rules to match the back-end logic

  TL-22272 Fixed the 'Record of Learning: Courses' report to ensure correct records for active and completed learning are displayed

    TL-20772 incorrectly applied report parameters which led to active courses
    appearing in the 'Completed learning' report for a user. This has now been fixed
    and the users will see only completed courses when viewing this report under
    their Record of Learning.

  TL-22577 Converted self-completion functionality on the course page to use a standard checkbox for improved accessibility

  TL-22825 Fixed incorrect licence information on the ChartJS doughnut label plugin

  TL-22832 Fixed the display of the manager's name on seminar event info page when the learner has more than one manager

  TL-22911 Fixed seminar event grade not being updated when a seminar event was cancelled or deleted

    The grading subsystem has been decoupled from the core seminar component. Grades
    and activity completion status will be updated through an event observer.
    Note that existing grades are not automatically recalculated on upgrade.

  TL-22947 Totara forms now wait for the previous submission to complete

    Previously some Totara forms could be submitted multiple times by clicking the
    save button in quick succession causing multiple records to be created.

  TL-23120 Fixed a bug that sent a booking confirmation when the attendance state was set to 'not set' on the taking attendance page

  TL-23157 Fixed inconsistent sorting of enrolment methods on the course enrolment page

  TL-23225 Adjusted popover's z-index to display in the correct stack order

    This will require CSS to be regenerated for themes that use LESS inheritance.

  TL-23234 Fixed profiling runs table being hidden from view

  TL-23520 Fixed shadow being hidden by graph image on the reports page

    This will require CSS to be regenerated for themes that use LESS inheritance.

  TL-23521 Removed styling  of report name when viewing the reports page

    This will require CSS to be regenerated for themes that use LESS inheritance.

  TL-23695 Fixed error in task block when a manager approval seminar event is deleted while approval is pending

  TL-23732 Ensured progress doughnuts are the same size

    This will require CSS to be regenerated for themes that use LESS inheritance.

  TL-23744 Fixed the issue where 'Report Manager' block was not displaying report correctly

  TL-23936 Ensured that when seminar events in the past are deleted, their associated calendar events are also removed

  TL-23969 Uploading seminar attendance records now correctly prevents changes to attendance status for archived attendees

  TL-24882 Fixed a PHP warning about non-numeric values when displaying graphs in report builder

  TL-25075 Saved search has been removed from the audience admin UI due to this functionality not being compatible with the embedded reports

API changes
-----------

  TL-9072  Refactored certification core code

    Introduced some separation around the transitions and creating completion
    records for certifications. This added specific functions for certification-only
    operations such as becoming certified, window opening and expiring. Conditions
    that were providing similar functionality in programs no longer work if the
    program being supplied to them is a certification, and throw an exception.

  TL-14412 Deprecated custom notification handling

    The following functions have been deprecated as part of this:
    * Function: totara_get_notifications() (alternative method:
      \core\notification::fetch())
    * Function: Function: totara_set_notification() (alternative: redirect or
      \core\notification::*())
    * Function: totara_convert_notification_to_legacy_array() (no alternative)
    * Function: totara_queue_append() (no alternative)
    * Function: totara_queue_shift() (no alternative)
    * Method: \core\notification::add_totara_legacy() (no alternative)

  TL-16531 Refactored internal 'totara_sync' code to use traits

  TL-16600 Deprecated the rest of facetoface_send_* functions

  TL-17311 Converted seminar CSS to use LESS

    IMPORTANT: This will require CSS to be regenerated for themes that use LESS
    inheritance.

  TL-18699 Separated the requested approval state into requested manager approval and requested role approval

    The requested approval state has been split into two separate states, requested
    manager approval state, and the requested role approval state.
    This allows for better control and transitioning when in a requested approval
    state.

  TL-19892 Abandoned DbUnit extension for PHPUnit has been removed

    phpunit_ArrayDataSet class no longer extends AbstractDataSet from DbUnit. Any
    PHPUnit tests in customisations that may be failing due to this change will need
    to be fixed by the developers.

  TL-20021 Deprecated event time status functions in facetoface

    Deprecated functions:
    * facetoface_allow_user_cancellation()
    * facetoface_is_adminapprover()
    * facetoface_get_manager_list()
    * facetoface_save_customfield_value()
    * facetoface_get_customfield_value()

    For more information, see mod/facetoface/upgrade.txt

  TL-20063 Converted seminar take attendance JavaScript from YUI module to AMD module

  TL-20331 Updated Basis notification icon definitions

    Previously the notification icon definitions provided by Basis did not include
    the component. This has now been corrected.

  TL-20362 Converted M.totara_plan_course_find from a YUI module to an AMD module

  TL-20363 Converted M.totara_plan_program_find from a YUI module to an AMD module

  TL-20364 Converted M.totara_plan_competency_find from a YUI module to an AMD module

  TL-20376 Deprecated date management functions related to facetoface

    Deprecated functions:
    1.  facetoface_save_dates()
    2.  facetoface_session_dates_check()

    For more information, see mod/facetoface/upgrade.txt

  TL-20377 Deprecated notification-related functions in mod/facetoface/lib.php

    Deprecated functions
    * facetoface_notify_under_capacity()
    * facetoface_notify_registration_ended()
    * facetoface_cancel_pending_requests()

    For more information, see mod/facetoface/upgrade.txt

  TL-20378 Deprecated environment functions related to facetoface 

    Deprecated functions:
    1.  facetoface_get_session()
    2.  facetoface_get_env_session()

    For more information, see mod/facetoface/upgrade.txt

  TL-20380 Deprecated export functionality within facetoface

    Deprecated functions:
    1.  facetoface_write_activity_attendance()
    2.  facetoface_get_user_customfields()

    For more information, see mod/facetoface/upgrade.txt

  TL-20381 Deprecated trivial facetoface functions

    Deprecated functions:
    * facetoface_allow_user_cancellation()
    * facetoface_is_adminapprover()
    * facetoface_get_manager_list()
    * facetoface_save_customfield_value()
    * facetoface_get_customfield_value()

    For more information, see mod/facetoface/upgrade.txt

  TL-20383 Deprecated seminar's attendees retriever functions

    Deprecated functions in mod_facetoface:
    * facetoface_get_attendee()
    * facetoface_get_requests()
    * facetoface_get_adminrequests()
    * facetoface_get_users_by_status()
    * facetoface_get_cancellations()
    * facetoface_get_num_attendees()
    * facetoface_get_user_submission()
    * facetoface_get_attendees()

    For more information and the replacements of the deprecated functions, see
    './mod/facetoface/upgrade.txt' 

  TL-20536 Added Behat steps for checking emails

    Developers can now write Behat steps that trigger the creation of emails which
    will be captured and can be examined for accuracy. These are the Behat steps
    available:
    1.  I reset the email sink
    2.  the following emails should have been sent
    3.  the following emails should not have been sent
    4.  I close the email sink

  TL-20542 The phar stream wrapper is now disabled by default during setup

    Phar is an advanced means of packaging and reading PHP code. It is not used by
    Totara, and in order to reduce the security surface area of the product we have
    disabled it by default.

    If you have a plugin or customisation that requires the phar stream wrapper to
    be available, we recommend you enable it in code immediately before it is
    required, and disable it again immediately afterwards.

  TL-20548 'runTemplateJS()' now returns an ES6 promise

    The 'runTemplateJS' function in the core/templates AMD library now returns an
    ES6 Promise once all UI components have been initialised

  TL-20749 New "ttr_tablename" syntax is allowed in SQL queries in addition to current {tablename}

    As well as using

    {tablename}

    in an SQL query it is now also possible to use "ttr_tablename".
    This enables SQL queries to be written that can be processed by code parsers and
    IDEs.
    Developers may want to consider using ttr_ as your default database prefix from
    now on.

  TL-20765 Added a new SQL class to improve handling of raw SQL in DML API

  TL-20819 Added a new interface for placeholder objects used within 'get_string()' calls

    Developers can now pass objects which implement 'core_string_placeholders' to
    the third parameter of 'get_string'. The replace function which these objects
    provide will be used to perform string placeholder substitution. This allows
    more powerful and complex placeholder systems to be implemented, in a consistent
    and reusable way. All values which could previously be passed as the third
    parameter of 'get_string' are still supported.

  TL-20857 Added method to clear visible notifications banners via JavaScript

  TL-20864 Upgraded jQuery to 3.4.1

    jQuery changelog can be found at
    https://blog.jquery.com/2019/04/10/jquery-3-4-0-released/

  TL-20918 Implemented new DML function 'set_fields' and 'set_fields_select' to update multiple fields in a table

  TL-20924 Updated PHPMailer to version 6.0.7

  TL-21024 Added support for enforced foreign key consistency

    Onupdate and ondelete referential integrity actions can now be added to foreign
    key relations. By default foreign keys are not enforced in any way.

    During definition of a foreign key using the XMLDB editor you can now choose to
    enforce referential integrity through set actions. The following actions are
    available:
    * 'restrict' blocks violation of foreign keys
    * 'cascade' propagates deletes
    * 'setnull' changes value to NULL

  TL-21040 Converted report_loglive YUI module to AMD module

    This removes the original YUI module.

  TL-21176 Upgraded chart.js library to version 2.8.0

  TL-21177 Added 'core/popover:destroy' event to the popover component

  TL-21222 Added support for deferring the creation of foreign keys

    This improvement extends TL-21024 which added support for enforcing foreign key
    relationships within install.xml.

    It is now possible to mark a foreign key relationship as deferred within
    install.xml, causing the system to skip the creation of the foreign key during
    installation. The developer is then responsible for creating the foreign key at
    the right time within an install.php file.

  TL-21230 Added a new transaction function in DML which accepts a Closure

    The new 'transaction()' method accepts a Closure which is automatically wrapped
    in a transaction. This is an alternative syntax to the traditional transaction
    handling.

  TL-21240 Extracted class 'program_utilities' into its own autoloaded class '\totara_program\utils'

  TL-21256 Nested transactions can be safely rolled back

    Previously transaction rollbacks were not supposed to be used from non-system
    code and they were not allowed at all in nested transactions.

    Rollback of individual nested transactions is now fully supported, and it is
    also not required to supply an exception when rolling back nested or main
    transactions.

  TL-21288 Relative file serving now facilitates file serving options including 'allowxss'

  TL-21327 Extracted program exceptions code into autoloaded classes \totara_program\exception\*

  TL-21368 Implemented formatters to be used in GraphQL type resolvers

    To simplify formatting of objects returned by GraphQL type resolvers a formatter
    can be used. It defines a map using field names for the keys and field format
    functions for the values. The formatter will get the value from the object, run
    it through the format function defined in the map and return the formatted
    value. Currently we support text (using format_text()), string (using
    format_string()) and date formatters. Custom field formatters can easily be
    implemented extending the base field formatter. 

    The existing helper functions format_text() and format_date()
    in \core\webapi\execution_context were deprecated in favour of the new field
    formatters \totara_core\formatter\field\text_field_formatter
    and \totara_core\formatter\field\date_field_formatter.

    Documentation: https://help.totaralearning.com/display/DEV/Formatters

  TL-21435 Removed typo3 library dependency from the 'core_text' class

  TL-21501 Replaced deprecated PHPExcel with PHPSpreadsheet library

  TL-21563 Removed 'portfolio_picasa' and 'repository_picasa' plugins that have been deprecated by Google

    In January 2019, Google deprecated its Picasa Web Albums Data API and disabled
    all associated OAuth scopes. In March 2019, the Picasa Web Albums API was
    completely turned off. We've removed the associated plugin and repository as
    they will no longer be functional.

  TL-21711 Extracted report builder content code into autoloaded classes \totara_reportbuilder\rb\content\*

  TL-21723 Added support for iteration over very large record sets to the DML layer

  TL-21810 Implemented performance metrics being returned in GraphQL query / mutation results if performance debugging is turned on

  TL-21822 Added a new 'Abstract' text field for report builder reports to allow content to be truncated and displayed correctly

  TL-21825 Implemented a cursor-based paginator for the ORM and DML

    The cursors paginator enables pagination using an opaque cursor. It can be used
    for paginating queries using a 'load more' approach to load the next set of
    results. 

    Using an opaque cursor also provides the benefit of encoding information used
    for classic offset-based pagination. 

    This patch includes a cursor paginator for the ORM and a paginator supporting
    classic offset-based pagination for both, the ORM and DML queries.

    For further information and documentation please refer to the paginator
    documentation in the public developer documentation.

  TL-21922 Introduced and applied prettier to .graphql and .grapqhls files

    This patch adds prettier support for .graphql and .graphqls files. It also adds
    a grunt task for it which is automatically run with grunt.

    Make sure you update your node modules via 'npm install'. To trigger prettier to
    format all graphql/graphqls files use './node_modules/.bin/grunt prettier'.

    Instructions on how to integrate prettier with your IDE can be found here:
    https://prettier.io/docs/en/editors.html.

  TL-21974 Added support for allowed values constraint on integer and character fields in database schema files

  TL-22069 Fixed a bug where dropping a test database had not been possible on MySQL due to foreign keys

  TL-22203 Upgraded PHP-CSS-Parser to 8.3.0, allowing for usage of calc()

  TL-22249 The 'Cancel' modal can now have a custom string for the 'Cancel' button

  TL-22250 Plugins can define multiple *.graphqls schema files within the webapi directory and all will be included

    Previously only one schema.graphqls file was supported. As the schema grows, the
    files can become quite large. You can now split up the file into multiple
    .graphqls files.

  TL-22255 Extend registration data to cover multi-tenancy usage

  TL-22399 Fixed majority of compatibility issues with PHP 7.4

  TL-22617 Move advanced feature checks from lib file into namespaced class

    The old functions were marked as deprecated: totara_feature_visible(),
    totara_feature_disabled()

    Please use \totara_core\advanced_feature::is_enabled() and
    \totara_core\advanced_feature::is_disabled() instead.

    Function totara_feature_hidden() is now deprecated without an alternative in the
    advanced_features class as the hidden status is no longer supported. Please use
    \totara_core\advanced_feature::is_disabled() instead.

    The advanced features settings page was modified to show the term 'Enable'
    instead of 'Show' to match the actual meaning of the setting. 

  TL-22665 Imported latest SimplePie 1.5.3 library

  TL-22803 Invalid composed unique indexes with nullable columns are now reported in PHPUnit test

  TL-22888 Improved the debugging message with human-readable callback name

    Prior to this patch, when an event or hook callback threw an exception, the
    debugging message to capture the error was not clear about the callback name,
    especially when the callback was specified using an array, or written as a
    closure.

    This patch makes it easier to see which callback threw an exception by providing
    a readable name in all cases.

  TL-23038 Improved sanitisation of content included when handling fatal errors

  TL-23121 Upgraded Bootstrap to 3.4.1

    Bootstrap JavaScript has been upgraded from version 3.3.4 to 3.4.1 and Bootstrap
    CSS has been upgraded from 3.3.7 to 3.4.1. This includes a number of minor bug
    fixes and improvements. All security issues had previously been backported.

  TL-23255 DML counted recordsets were completely deprecated, use two separate queries for record counting instead

  TL-23279 Added 'to_array()' method to database recordset

  TL-23280 Improved the handling of recordsets in HR Import

    Made sure that database recordsets are closed properly in HR Import
    hierarchy.element.class.php

  TL-23322 Imported HTML Purifier 4.12.0

  TL-23323 Imported TCPDF 6.3.2

  TL-23324 Imported AdoDB 5.20.15

  TL-23336 Upgraded PHP-CSS-Parser, fixing an issue with RTLCSS control comments introduced by the previous upgrade to 8.3.0

  TL-23339 Added redirecting hooks functionality in phpunit tests

    Analogue to the events hooks can now be intercepted in unit tests to be able to
    check if a hook was executed.

    Use '$sink = $this->redirectHooks();' to turn redirection on and all hooks
    executed after that will land in the hook sink. No watchers will be called.

  TL-23405 Added '_unkeyed' functions to the DML select functions

    This allows the return of database results without keying by the first column
    without using recordsets. This has no significant performance impact compared to
    using a recordset.

  TL-23453 Added support for reversed order in core_collator, core_collator::SORT_REGULAR constant was changed to 128

  TL-23460 Data fetching from MSSQL database has been rewritten to improve reliability

    Please note it may be required to increase available PHP memory compared to
    previous releases.

  TL-23494 Imported mustache library version 2.13.0

  TL-23507 Updated markdown library to version 1.9.0

  TL-23551 Moved seminar assets and deprecated methods into traits

  TL-23554 Updated PHPMailer to version 6.1.4

  TL-23645 Backport mod_lti changes from Moodle 3.8.1

  TL-23658 Changed content marketplace to allow non-integer learning object keys

    The function 'get_learning_object' in the search class has been changed. Instead
    of an integer, it now expects a string. All custom content marketplaces will
    need to update their function signatures to match this change, otherwise an
    error will be reported when trying to fetch learning object details. After
    making a change to your custom content marketplace, you can check that the
    change was successful by going to Explore Content, clicking a learning object,
    and seeing that the details load successfully.

    IMPORTANT: This is a breaking change. All custom content marketplaces will need
    to be reviewed.

  TL-24120 Added a String.fromCodePoint polyfill in JavaScript for IE 11

  TL-24172 Increased the maximum length of database column and table names

    Maximum database column name length is now 63 characters. Maximum database table
    name length is now 48 characters.

  TL-24318 Updated TCPDF library to version 3.6.5

  TL-24602 Deprecated the 'Switch to other roles' capability

    All related functionality will be removed in Totara 14

  TL-25277 Deprecated Memcache session handler, use Memcached handler instead

  TL-25394 Upgraded PHPUnit testing framework to version 8.5

  TL-25771 Disabled report builder caching when content restrictions are enabled

    This change was required as report builder must rely upon live data when
    resolving content restrictions. Cached report data is often not in sync with
    live data and attempting to resolve relationships across the combined dataset
    can lead to errors.

    If you have an existing report configured to use both content restrictions and
    caching, the caching will no longer be used.

    Should you be using report caching in Totara 12 due to the performance of any
    report source, please inform us via the help desk so that we can review the
    report source with the aim of improving its overall performance.

  TL-26918 Added to_array function for context class

    The to_array function allows easy access to full detail of context object when
    needed. This is useful in functions such as json_encode that ignore objects with
    protected properties.

  TL-27162 Created data generation scripts to generate data for performance testing

Miscellaneous Moodle fixes
--------------------------

  TL-20490 MDL-64971: Ensure that the capability exists when fetching

  TL-22359 Backport useful accesslib improvements from Moodle 3.4-3.8: Multiple accesslib improvements merged from Moodle

    * MDL-61875 core_component: new method to get full components list
    * MDL-61441 accesslib: get_users_by_capability groups perf improvement
    * MDL-46783 permissions: let some moodle/ caps be overriden in all mods
    * MDL-63818 core: Add all relevant module context caps
    * MDL-54035 accesslib: Rewritten cache invalidation
    * MDL-62065 core_access: deprecation of get roles on exact context
    * MDL-62747 accesslib: rdef caching should handle roles with no caps
    * MDL-60043 accesslib: improve query performance by removing ORDER BY
    * MDL-59897 Accesslib: get_user_capability_course is slow

  TL-22499 Selective Moodle 3.4.9 merge: Upgrade path from Moodle 3.4.9 to Totara 13

    Moodle 3.4.9 sites can be upgraded to Totara 13.
    Bug fixes and improvements have been selectively cherry-picked. Each change
    cherry-picked is separately noted in the changelogs.

  TL-22512 Cherry pick Global search changes from 3.4.9: Multiple fixes and improvements for experimental Global search

  TL-22515 MDL-40838: New options to restore enrolment methods without users

  TL-22534 Cherry pick new file type restrictions from 3.4.9: New options to restrict uploaded file types

  TL-22536 MDL-58567: Upgrades now show time it took to run each upgrade step

  TL-22538 MDL-50011: New system wide default setting page for the multichoice question

  TL-22539 MDL-36501: New checkbox for extra credit when adding a grade item

  TL-22541 MDL-58820: Response numbering styling is now allowed in MCQs

  TL-22542 MDL-59125: nolink class is now respected by urltolink filter

  TL-22543 MDL-59427: Purge ad-hoc caches when purging all caches

  TL-22544 MDL-58851: LTI administration capability was added

  TL-22566 MDL-59323: Fixed database check for defaults of character fields

  TL-22634 MDL-57991: Improved rendering of media players

  TL-22635 MDL-59460: Added new setting for default subscription mode in forum activity

  TL-22636 MDL-59572: error_log is now used for AJAX/WS calls in DML layer

  TL-22652 MDL-59702: User identity display was fixed in lesson activity overview report

  TL-22657 MDL-59084: All ad-hoc tasks now run using the original user id by default

  TL-22662 MDL-57115: The messages block has been removed from the Totara distribution

  TL-22663 MDL-60197: All/none option is now shown only if necessary in database activity export

  TL-22668 Import latest HTMLPurifier: HTMLPurifier upgraded to latest version 4.11.0

  TL-22671 Import latest ADOdb 5.20.14: Imported ADOdb 5.20.14 library

  TL-22676 MDL-59274: Imported more recent video.js library and plugins

  TL-22677 MDL-60209: Multiple fixes and improvements in MathJax integration

  TL-22682 MDL-59844: Bearer auth method was added to WebDAV requests

  TL-22685 MDL-60268: Made location for resetting user tours explicit

  TL-22687 MDL-46269: New $casesensitive argument added to the sql_regex() method

  TL-22688 MDL-57455: Implemented tagging in database activity

  TL-22689 MDL-60211: Added new filters for category, course, and course type to user tours

  TL-22690 MDL-60116: Password reset email now contains user name

  TL-22698 MDL-52538: Fixed grade info displayed when ongoing score disabled

  TL-22701 MDL-61081: Added start and end date for courses created by external db

  TL-22702 MDL-60547: Prevented scroll jump when 'Mark all as read' is clicked in notifications drop down

  TL-22708 MDL-57456: Implemented tagging in glossary activity

  TL-22712 MDL-57742: Improved lti compatibility by making tool_consumer_instance_guid optional to match the specification

  TL-22717 MDL-31443: Improved labels on backup and restore pages

  TL-22719 MDL-57968: Improved performance by optimising new message notification

  TL-22722 MDL-62320: JSON format was added to the default mime types list

  TL-22730 MDL-61786: Test interface for external authentication settings can now be localised

  TL-22735 MDL-62325: Added some keys and indexes to enrol_paypal

  TL-22737 MDL-61296: Fixed notices when LDAP authentication is misconfigured

  TL-22740 MDL-62772: Plagiarism disclosure info is shown to all users in assignment

  TL-22743 MDL-53537: New event is triggered when course is backed up

  TL-22752 MDL-62771: Plagiarism disclosure information is always displayed on forum if enabled, not just for first post

  TL-22754 MDL-59611: Moved help icons for subplugin types out of labels in admin UI

  TL-22755 MDL-57666: Fixed display of error in Dropbox repository

  TL-22756 MDL-62750: Invalid whitespace is now stripped from the langlist setting value

  TL-22758 MDL-43334: Fixed issues with Cancel backup button

  TL-22759 MDL-62643: Empty online text files are now skipped when providing feedback

  TL-22760 MDL-61650: Made the assignment module check for empty text more consistent

  TL-22761 MDL-57741: Added workaround for LTI launch without cartridge support

  TL-22763 MDL-62867: Improved tags performance

  TL-22770 MDL-50314: Quiz reports now honour 'showuseridentity' setting when exporting

  TL-22774 MDL-58781: Improved consistency of feedback analysis export

  TL-22776 MDL-62717: Descriptions of automatically created announcement forums are now using HTML format

  TL-22782 MDL-51969: Improved compatibility of LTI module with some webservers

  TL-22784 MDL-63456: Improved Aiken question type error handling

  TL-22787 MDL-60897: Fixed handling of invalid Cloze questions

  TL-22795 MDL-62702: Improved LTI provider consistency checks

  TL-22805 MDL-57457: Implemented book tagging

  TL-22807 MDL-46929: Implemented tagging in forum activities

  TL-22818 MDL-33483: Improved file import from Google docs repository

  TL-22819 MDL-47354: Option to select number of items per page was added to Single view gradebook report

  TL-22820 MDL-42266: More options were added to the list of maximum file size settings

Contributions
-------------

  * Chris Wharton at Catalyst EU - TL-8300
  * Jamie Kramer at Elearning Experts - TL-7394
  * Jo Jones at Kineo UK - TL-19815
  * Mark Ward at Learning Pool - TL-5081
  * Russell England at Kineo USA - TL-20756
