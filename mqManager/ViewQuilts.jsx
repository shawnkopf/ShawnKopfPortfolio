import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head } from "@inertiajs/react";
import {
    Card,
    Grid,
    Title,
    Text,
    Tab,
    TabList,
    TabGroup,
    TabPanel,
    TabPanels,
    Flex,
    Badge,
} from "@tremor/react";
import BasicKpiCard from "@/Components/BasicKpiCard.jsx";
import QuiltTable from "@/Components/QuiltTable.jsx";
import PrintQueue from "@/Components/PrintQueue";
import { useState, useEffect } from "react";

export default function Dashboard({ auth, quiltLists }) {
    const [checkedInQuiltsCount, setCheckedInQuiltsCount] = useState(0);
    const [checkedInQuilts, setCheckedInQuilts] = useState([]);

    const fetchCheckedInQuilts = () => {
        const storedQuilts =
            JSON.parse(localStorage.getItem("printedQuilts")) || [];

        setCheckedInQuilts(storedQuilts);
        setCheckedInQuiltsCount(storedQuilts.length);
    };

    useEffect(() => {
        fetchCheckedInQuilts();
    }, []);

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <h2 className="font-semibold text-xl text-gray-800 leading-tight">
                    View Quilts
                </h2>
            }
        >
            <Head title="Quilts" />

            <main className="px-3 py-8">
                <Title>Sundance Quilting</Title>
                <Text>MqManager v2</Text>

                <TabGroup className="mt-6">
                    <TabList>
                        <Tab>
                            Not Received ({quiltLists.notReceivedQuilts.total})
                        </Tab>
                        <Tab>Received ({quiltLists.receivedQuilts.total})</Tab>
                        <Tab>Staged ({quiltLists.stagedQuilts.total})</Tab>
                        <Tab>Quilted ({quiltLists.quiltedQuilts.total})</Tab>
                        <Tab>Trimmed ({quiltLists.trimmedQuilts.total})</Tab>
                        <Tab>Bound ({quiltLists.boundQuilts.total})</Tab>
                        <Tab>Shipped ({quiltLists.shippedQuilts.total})</Tab>
                        <Tab>Checked-In Quilts ({checkedInQuiltsCount})</Tab>
                    </TabList>
                    <TabPanels>
                        <TabPanel>
                            <div className="mt-6">
                                <QuiltTable
                                    title={
                                        "Not Received (" +
                                        quiltLists.notReceivedQuilts.total +
                                        ")"
                                    }
                                    quilts={quiltLists.notReceivedQuilts}
                                />
                            </div>
                        </TabPanel>
                        <TabPanel>
                            <div className="mt-6">
                                <QuiltTable
                                    title={
                                        "Received Quilts (" +
                                        quiltLists.receivedQuilts.total +
                                        ")"
                                    }
                                    quilts={quiltLists.receivedQuilts}
                                />
                            </div>
                        </TabPanel>
                        <TabPanel>
                            <div className="mt-6">
                                <QuiltTable
                                    title={
                                        "Staged Quilts (" +
                                        quiltLists.stagedQuilts.total +
                                        ")"
                                    }
                                    quilts={quiltLists.stagedQuilts}
                                />
                            </div>
                        </TabPanel>
                        <TabPanel>
                            <div className="mt-6">
                                <QuiltTable
                                    title={
                                        "Quilted Quilts (" +
                                        quiltLists.quiltedQuilts.total +
                                        ")"
                                    }
                                    quilts={quiltLists.quiltedQuilts}
                                />
                            </div>
                        </TabPanel>
                        <TabPanel>
                            <div className="mt-6">
                                <QuiltTable
                                    title={
                                        "Trimmed Quilts (" +
                                        quiltLists.trimmedQuilts.total +
                                        ")"
                                    }
                                    quilts={quiltLists.trimmedQuilts}
                                />
                            </div>
                        </TabPanel>
                        <TabPanel>
                            <div className="mt-6">
                                <QuiltTable
                                    title={
                                        "Bound Quilts (" +
                                        quiltLists.boundQuilts.total +
                                        ")"
                                    }
                                    quilts={quiltLists.boundQuilts}
                                />
                            </div>
                        </TabPanel>
                        <TabPanel>
                            <div className="mt-6">
                                <QuiltTable
                                    title={
                                        "Shipped Quilts (" +
                                        quiltLists.shippedQuilts.total +
                                        ")"
                                    }
                                    quilts={quiltLists.shippedQuilts}
                                />
                            </div>
                        </TabPanel>
                        <TabPanel>
                            <div className="mt-6">
                                <PrintQueue
                                    checkedInQuilts={checkedInQuilts}
                                    setCheckedInQuilts={setCheckedInQuilts}
                                    setCheckedInQuiltsCount={
                                        setCheckedInQuiltsCount
                                    }
                                    fetchCheckedInQuilts={fetchCheckedInQuilts}
                                />
                            </div>
                        </TabPanel>
                    </TabPanels>
                </TabGroup>
            </main>
        </AuthenticatedLayout>
    );
}
