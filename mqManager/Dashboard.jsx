import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import {
    Card,
    Grid,
    Title,
    Text,
    Tab,
    TabList,
    TabGroup,
    TabPanel,
    TabPanels, Flex, Badge,
} from "@tremor/react";
import BasicKpiCard from "@/Components/BasicKpiCard.jsx";
import QuiltTable from "@/Components/QuiltTable.jsx";

export default function Dashboard({ auth, quiltLists, receivedQuiltsCount, awaitingReceiptCount,  }) {
    return (
        <AuthenticatedLayout
            user={auth.user}
            header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>}
        >
            <Head title="Dashboard" />

            <main className="px-3 py-8">
                <Title>Sundance Quilting</Title>
                <Text>MqManager v2</Text>

                <TabGroup className="mt-6">
                    <TabList>
                        <Tab>Overview</Tab>
                        <Tab>NOT Quilts</Tab>
                    </TabList>
                    <TabPanels>
                        <TabPanel>
                            <Grid numItemsMd={2} numItemsLg={3} className="gap-6 mt-6">
                                <BasicKpiCard
                                    title="Quilts Received"
                                    value={receivedQuiltsCount}
                                    helperText="unfinished quilts in our warehouse"
                                />
                                <BasicKpiCard
                                    title="Quilts Not Receieved"
                                    value={awaitingReceiptCount}
                                    helperText="quilts ordered that we haven't received"
                                />
                                <BasicKpiCard
                                    title="Quilts in Danger Zone"
                                    value={quiltLists.dangerZone.length}
                                    helperText="quilts that need quilted in the next 2 days"
                                />
                            </Grid>
                            <div className="mt-6">
                                <Card>
                                    <Text>Quilt Queue</Text>
                                    <TabGroup>
                                        <TabList className="mt-8">
                                            <Tab>This Week <Badge size="sm">{quiltLists.thisWeekQueue.length}</Badge></Tab>
                                            <Tab>Next Week <Badge size="sm">{quiltLists.nextWeekQueue.length}</Badge> </Tab>
                                        </TabList>
                                        <TabPanels>
                                            <TabPanel>
                                                <div className="mt-10">
                                                    <Flex className="mt-4">
                                                        <QuiltTable
                                                            title="This Week"
                                                            quilts={quiltLists.thisWeekQueue}
                                                        />
                                                    </Flex>
                                                </div>
                                            </TabPanel>
                                            <TabPanel>
                                                <div className="mt-10">
                                                    <Flex className="mt-4">
                                                        <QuiltTable
                                                            title="Next Week"
                                                            quilts={quiltLists.nextWeekQueue}
                                                        />
                                                    </Flex>
                                                </div>
                                            </TabPanel>
                                        </TabPanels>
                                    </TabGroup>
                                </Card>

                            </div>
                        </TabPanel>
                        <TabPanel>
                        </TabPanel>
                    </TabPanels>
                </TabGroup>
            </main>

        </AuthenticatedLayout>
    );
}
