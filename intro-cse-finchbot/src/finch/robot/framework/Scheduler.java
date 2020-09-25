package finch.robot.framework;

import finch.robot.Robot;

import java.util.ArrayList;
import java.util.Scanner;

/**
 * Created by yoseph on 5/10/2016.
 * A class that contains the robot thread, manages all commands, and runs the robot code.
 */
public class Scheduler implements Runnable {

    /* Scheduler Instance */
    private static Scheduler scheduler;
    /* Static Methods */
    private final Thread thread;
    /**
     * The refresh rate of the program in milli seconds.
     */
    private final long rate = 20;
    private final CommandScheduler commandScheduler;
    private Robot robot;
    private ArrayList<Subsystem> subsystems;
    private volatile boolean running = false;

    /**
     * Starts the Scheduler, Client, and Command Scheduler thread.
     */
    private Scheduler() {
        subsystems = new ArrayList<Subsystem>();
        commandScheduler = new CommandScheduler();
        thread = new Thread(this);
        running = true;
        thread.start();


    }

    public static void main(String[] args) {
        scheduler = new Scheduler();
        Scanner scanner = new Scanner(System.in);
        String s = scanner.nextLine();
        if(s.toLowerCase().contains("stop"))
            Scheduler.getInstance().stop();
    }

    public static Scheduler getInstance() {
        return scheduler;
    }

    @Override
    public void run() {
        commandScheduler.start();
        System.out.println("Starting robot.");
        robot = Robot.getInstance();
        for (int i = 0; i < subsystems.size(); i++) {
            subsystems.get(i).init();
        }
        Robot.getInstance().getWindow().addText("Robot Started");
        robot.init();
        long begin = System.nanoTime();
        long time = 0;
        long wait = 0;
        while (running) {
            time = System.nanoTime();

            wait = rate - (time - begin);

            if (wait < 0)
                wait = 5;

            try {
                thread.wait(wait);
            } catch (Exception e) {
            }
            robot.update();

        }
        System.out.println("Stopping robot components.");
        robot.getWindow().addText("Stopping robot components");
        commandScheduler.stop();
        System.out.println("Waiting for other threads to end...");
        while (!commandScheduler.isStopped());
        for (int i = 0; i < subsystems.size(); i++) {
            subsystems.get(i).end();
        }
        robot.end();
        System.out.println("Robot stopped");
        System.exit(0);// Commented out to not have the program abandon the terminal while other threads are running.
    }

    /**
     * adds a subsystem to the program to keep track of commands running on them.
     *
     * @param subsystem The added subsystem.
     */
    public void addSubsystem(Subsystem subsystem) {
        subsystems.add(subsystem);
    }

    /**
     * @return The CommandScheduler.
     */
    public synchronized CommandScheduler getCommandScheduler() {
        return commandScheduler;
    }

    /**
     * Peacefully stops the thread by waiting until this current iteration is over then calls the finalization methods
     * and no longer continues to run.
     */
    public synchronized void stop() {
        running = false;
    }
}
